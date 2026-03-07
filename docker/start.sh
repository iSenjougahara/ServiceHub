#!/bin/bash

cd /var/www/html

# Start Vite in background
npm run dev -- --host 0.0.0.0 &

# Start Laravel
php artisan serve --host=0.0.0.0 --port=8000