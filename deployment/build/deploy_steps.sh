#!/usr/bin/env bash

set -euo pipefail

PROJECT_DIR="/var/www/simpus"

cd "${PROJECT_DIR}"

echo "==> Installing PHP dependencies"
composer install --no-dev --optimize-autoloader

echo "==> Running database migrations"
php artisan migrate --force

echo "==> Caching configuration"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Building front-end assets"
npm ci
npm run build

echo "==> Clearing stale caches"
php artisan queue:restart || true
php artisan cache:clear

echo "Deployment steps completed."
