# ---- Etapa 1: compilar assets (Vite/Vue) ----
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

# ---- Etapa 2: imagen de runtime (FrankenPHP) ----
FROM dunglas/frankenphp:1-php8.2

RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    gd \
    zip \
    opcache

# El binario trae cap_net_bind_service para poder usar el puerto 80/443 sin
# root — pero nosotros escuchamos en el puerto que asigna Render ($PORT, no
# privilegiado), y su entorno (sandbox estilo gVisor) rechaza ejecutar
# binarios con capabilities con "Operation not permitted". Se la quitamos.
RUN apt-get update && apt-get install -y --no-install-recommends libcap2-bin \
    && setcap -r /usr/local/bin/frankenphp \
    && apt-get purge -y libcap2-bin && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache

RUN chmod +x docker/entrypoint.sh

ENTRYPOINT ["docker/entrypoint.sh"]
