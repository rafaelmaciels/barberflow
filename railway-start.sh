#!/bin/bash

echo "Configuring SQLite database..."
touch storage/database.sqlite
chmod 777 storage/database.sqlite

echo "Running migrations..."
php artisan migrate --force

echo "Starting web server..."
if [ -f "/assets/scripts/start.sh" ]; then
    echo "Using Nixpacks default Nginx..."
    bash /assets/scripts/start.sh
else
    echo "Falling back to Artisan serve..."
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
fi
