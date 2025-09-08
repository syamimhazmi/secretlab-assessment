#!/bin/sh

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

sleep 5

echo "Checking APP_KEY..."
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is not set!"
    exit 1
fi

php artisan optimize

php-fpm -D &&  nginx -g "daemon off;"
