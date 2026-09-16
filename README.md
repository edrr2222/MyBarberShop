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
routes/web-snippet.php     -> rutas a fusionar con routes/web.php
resources/js/components/   -> ClientQr.vue, EmpleadoScanner.vue
config/auth-guards-snippet.php -> snippet para config/auth.php (3 guards)
```

## Cómo integrarlo en un proyecto Laravel

1. Si aún no tienes el proyecto Laravel instalado:
   ```bash
   composer create-project laravel/laravel .
   ```

2. Copia las carpetas `database/migrations` y `database/procedures` dentro de tu proyecto (fusiona con lo que ya exista).

3. Configura tu conexión Postgres en `.env`:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=...
   DB_PORT=5432
   DB_DATABASE=...
   DB_USERNAME=...
   DB_PASSWORD=...
   ```

4. Corre las migraciones:
   ```bash
   php artisan migrate
   ```
   Esto crea los schemas (`tenant`, `barberia`, `loyalty`), todas las tablas, y carga los 3 stored procedures.

5. Copia el contenido de `config/auth-guards-snippet.php` dentro de tu `config/auth.php` real (arrays `guards` y `providers`).

6. Copia `app/Models`, `app/Http/Controllers` y `app/Http/Middleware` a tu proyecto.

7. Registra el middleware `tenant`:
   - **Laravel 11+** (`bootstrap/app.php`):
     ```php
     ->withMiddleware(function (Middleware $middleware) {
         $middleware->alias(['tenant' => \App\Http\Middleware\ResolveTenant::class]);
     })
     ```
   - **Laravel 10 o anterior** (`app/Http/Kernel.php`, array `$middlewareAliases`):
     ```php
     'tenant' => \App\Http\Middleware\ResolveTenant::class,
     ```

8. Fusiona `routes/web-snippet.php` dentro de tu `routes/web.php`.

9. Crea las vistas Blade referenciadas en las rutas (`client.landing`, `client.qr`, `empleado.login`, `empleado.scanner`, `empleado.perfil`, `admin.login`, `admin.dashboard`) — pueden empezar como placeholders simples.

10. Instala las dependencias JS de los componentes Vue:
    ```bash
    npm install qrcode html5-qrcode
    ```
    `ClientQr.vue` pinta el QR dinámico (se regenera antes de expirar). `EmpleadoScanner.vue` abre la cámara, escanea, llama a `/api/loyalty/escanear` y muestra el mensaje de "¡CORTE GRATIS!" cuando corresponde.

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

- Vistas Blade (landing, logins, dashboards).
- Panel admin: CRUD de sede, empleados, servicios; configuración de logo/colores; generación descargable del QR fijo de sede.
- Theming dinámico (variables CSS `--color-primario`/`--color-secundario` desde los datos de la barbería).
- Corrección/anulación de un sello aplicado por error (fuera de alcance del MVP).
- Deploy en Render (Web Service + Postgres free tier).
