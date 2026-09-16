#!/bin/sh
set -e

php artisan config:clear
php artisan db:prepare-schema
php artisan migrate --force
php artisan storage:link || true

exec frankenphp run --config /app/docker/Caddyfile
