#!/usr/bin/env bash
set -euo pipefail

# Render injects the port to bind to via $PORT.
PORT="${PORT:-8080}"
sed -ri "s/^Listen [0-9]+$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

export APP_URL="${APP_URL:-${RENDER_EXTERNAL_URL:-http://localhost:${PORT}}}"

touch database/database.sqlite

php artisan package:discover --ansi
php artisan optimize

chown -R www-data:www-data storage bootstrap/cache database

exec "$@"
