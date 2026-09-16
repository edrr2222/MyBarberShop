# MyBarberShop

App web multitenant de fidelización para barberías: reemplaza la tarjeta física de sellos por una tarjeta virtual. El cliente muestra un QR dinámico desde su celular, el barbero lo escanea desde su propia app, y al llegar a **7 sellos** (configurable por barbería) se desbloquea un corte gratis.

## Stack

- **Backend**: Laravel + PostgreSQL, con la lógica crítica de sellos/QR implementada en **stored procedures (plpgsql)** en lugar de PHP.
- **Frontend**: Vue.js, componentes `.vue` servidos desde el mismo Laravel (sin proyecto Vue separado).
- **Deploy objetivo**: Render (free tier).
- **Idioma**: todo el código de dominio (tablas, columnas, rutas, mensajes al usuario) está en español.

## Modelo de datos

La base de datos está organizada en 3 schemas de Postgres:

- `tenant.*` — barberia, sede, client, admin
- `barberia.*` — empleado, empleado_red_social, servicio
- `loyalty.*` — config, card, stamp, qr_token

### Reglas de negocio clave

- **Multitenancy**: una barbería (`tenant.barberia`) puede tener varias sedes (`tenant.sede`).
- **Sellos por barbería, no por sede**: un cliente junta sellos en cualquier sede de la misma barbería y puede redimir en cualquiera.
- **Cédula única por barbería** (no global): el mismo cliente puede registrarse en distintas barberías de la plataforma.
- **3 tipos de cuenta independientes**, cada uno con su propio guard de Laravel: `client`, `empleado`, `admin`.
- **QR del cliente dinámico**: token UUID de un solo uso que expira en `loyalty.config.qr_token_segundos` (default 60s), pensado para evitar fraude.
- **QR fijo de sede**: link estático (`/b/{barberiaSlug}/{sedeSlug}`) para imprimir y pegar en el local.
- **Admin** puede administrar una barbería completa o una sede específica.

Toda la lógica de sellos vive en 3 stored procedures (`database/procedures/loyalty_procedures.sql`):
`loyalty.fn_generar_token`, `loyalty.fn_escanear`, `loyalty.fn_redimir`. Los controllers de Laravel solo los invocan vía `DB::select`.

## Estructura del proyecto

```
database/migrations/      -> migraciones (schemas, tablas, carga de stored procedures)
database/procedures/      -> loyalty_procedures.sql (fuente de los SPs)
app/Models/                -> Eloquent models, uno por tabla
app/Http/Middleware/       -> ResolveTenant.php (resuelve barbería+sede por slug)
app/Http/Controllers/      -> TenantController, *AuthController (x3 guards), QrController
app/Http/Controllers/Admin/ -> CRUD de marca, sedes, empleados, servicios, clientes
routes/web.php             -> rutas de la app (tenant, client, staff, admin, api/loyalty)
resources/js/components/   -> ClientQr.vue, EmpleadoScanner.vue (montados con Vite)
resources/views/           -> vistas Blade (client, empleado, admin) con identidad de peluquería
config/auth.php            -> guards client/empleado/admin ya configurados
```

## Cómo correrlo localmente

1. Instala dependencias PHP y JS:
   ```bash
   composer install
   npm install
   ```

2. Copia `.env.example` a `.env` y ajusta la conexión a tu Postgres local:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=mybarbershop
   DB_USERNAME=postgres
   DB_PASSWORD=...
   ```
   Genera la app key si hace falta: `php artisan key:generate`.

3. Crea la base de datos y corre las migraciones:
   ```bash
   createdb mybarbershop
   php artisan migrate
   ```
   Esto crea los schemas (`tenant`, `barberia`, `loyalty`), todas las tablas, y carga los 3 stored procedures.

4. Levanta el servidor:
   ```bash
   php artisan serve
   ```
   La app queda en `http://127.0.0.1:8000`. Rutas para probar: `/`, `/staff/login`, `/admin/login`, y `/b/{barberiaSlug}/{sedeSlug}` (requiere una fila en `tenant.barberia`/`tenant.sede` con `estado = true`).

Los componentes Vue del QR y el scanner (`ClientQr.vue`, `EmpleadoScanner.vue`) ya están montados vía Vite en `client/qr` y `empleado/scanner` — no requieren pasos extra.

## Panel admin

Desde `/admin/login` el administrador puede gestionar:

- **Marca** (`/admin/marca`): nombre, colores (primario/secundario/terciario) y logo — solo visible para el admin de barbería completa (`sede_id` null).
- **Sedes**: crear/editar sedes, activar/desactivar. Un admin de sede específica solo ve y edita su propia sede.
- **Empleados**: crear/editar (incluye reseteo de contraseña), asignar a sede, activar/desactivar.
- **Servicios**: nombre, precio, duración, si aplica sello de fidelidad, activar/desactivar.
- **Clientes**: listado con búsqueda por nombre/cédula, sellos actuales, activar/desactivar cuenta.

Los colores configurados en Marca se propagan a todas las pantallas (cliente, empleado, admin) vía variables CSS. Las redes sociales configuradas ahí (Instagram, WhatsApp, etc.) se muestran como íconos en el pie de las páginas de cliente y empleado.

## Deploy en Render

El proyecto incluye `Dockerfile` + `render.yaml` para desplegar como Web Service (Docker) con una base de datos Postgres administrada. La imagen usa [FrankenPHP](https://frankenphp.dev/) (PHP + Caddy en un solo binario) — más liviano que montar Nginx + PHP-FPM por separado, y suficiente para el tráfico de un MVP.

### Opción 1: Blueprint (recomendado, un clic)

1. En Render: **New → Blueprint**, conecta este repositorio. Render detecta `render.yaml` y crea el Web Service + la base de datos Postgres juntos.
2. Antes de confirmar (o justo después, en el dashboard del servicio), define las dos variables marcadas `sync: false`:
   - `APP_KEY`: genera un valor localmente con `php artisan key:generate --show` y pégalo tal cual (incluye el prefijo `base64:`).
   - `APP_URL`: déjala vacía en el primer deploy; una vez Render asigne la URL (ej. `https://mybarbershop.onrender.com`), actualízala con esa URL y vuelve a desplegar (afecta las URLs de los assets y del logo).
3. Render construye la imagen, corre el contenedor, y el propio `docker/entrypoint.sh` ejecuta `php artisan migrate --force` (crea los 3 schemas y carga los stored procedures) antes de levantar el servidor.

### Opción 2: manual

1. Crea una base de datos Postgres en Render (free tier).
2. Crea un Web Service apuntando a este repo, entorno **Docker** (usa el `Dockerfile` de la raíz).
3. Configura las variables de entorno (ver `render.yaml` para la lista completa): `APP_KEY`, `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`, `APP_LOCALE=es`, y las `DB_*` apuntando a la base de datos creada en el paso 1.
4. Deploy. El healthcheck usa `/up` (ya viene configurado en `bootstrap/app.php`).

### Limitaciones a tener en cuenta en free tier

- **Los logos subidos desde Marca no persisten**: el filesystem del contenedor es efímero — cualquier redeploy o reinicio por inactividad borra lo subido a `storage/app/public`. Los discos persistentes son una función paga de Render. Para producción real, mover el disco `public` a un servicio externo (S3, Cloudinary, etc.).
- Ver también la sección siguiente sobre las demás limitaciones del free tier (sleep, expiración de Postgres, cron).

## Flujo de sellado (resumen)

1. Cliente pide ver su QR → `SELECT * FROM loyalty.fn_generar_token(client_id, barberia_id, 60)`
2. Barbero escanea → `SELECT * FROM loyalty.fn_escanear(token, empleado_id, sede_id, servicio_id)` — retorna `corte_gratis = true` cuando se completa la tarjeta.
3. Al aplicar el corte gratis → `SELECT * FROM loyalty.fn_redimir(card_id)`

## Limitaciones del entorno de deploy (Render free tier)

- El Web Service se duerme tras 15 min de inactividad (~30-50s en despertar) → el token QR tiene margen de 60-90s.
- Postgres free expira a los 90 días — válido para MVP/demo, no para producción.
- Sin cron jobs gratis → los `qr_token` vencidos se filtran con `WHERE expires_at > now()` en las queries, sin job de limpieza.
- Sin subdominios personalizados en free tier → el tenant se resuelve por ruta (`/b/{barberiaSlug}/{sedeSlug}`), no por subdominio.

## Pendiente

- Generación descargable del QR fijo de sede desde el panel admin.
- Corrección/anulación de un sello aplicado por error (fuera de alcance del MVP).
- Almacenamiento persistente para el logo (ver limitación de Render arriba).
