#!/bin/sh
set -e

cd /var/www/html

mkdir -p storage/app/public storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan package:discover --ansi || true
php artisan config:cache
php artisan route:cache || true
php artisan view:cache

exec supervisord -c /etc/supervisor/conf.d/app.conf
