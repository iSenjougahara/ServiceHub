#!/bin/bash

cd /var/www/html

# Clear cached config to ensure .env is read fresh
php artisan config:clear

# Run migrations
php artisan migrate --force

# Start Vite in background
npm run dev &

# Start queue worker in background
php artisan queue:work --tries=3 --timeout=60 &

# Start Laravel
php artisan serve --host=0.0.0.0 --port=8000