#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

DB_FILE="/var/www/html/database/database.sqlite"
if [ ! -f "$DB_FILE" ]; then
    echo "Creating empty SQLite database file at $DB_FILE..."
    mkdir -p /var/www/html/database
    touch "$DB_FILE"
fi

mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Generate Application Key if not present
if ! grep -q "^APP_KEY=base64:" .env && [ -z "$APP_KEY" ]; then
    echo "Generating application encryption key..."
    php artisan key:generate --force
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed sample data if table is fresh or SEED_ON_START is true
if [ "$SEED_ON_START" = "true" ] || ! php artisan tinker --execute 'exit(App\Models\Product::count() > 0 ? 0 : 1);' >/dev/null 2>&1; then
    echo "Seeding initial Quicksave Agencies water packaging & distribution data..."
    php artisan db:seed --force || echo "Seeder finished or already populated."
fi

# Link storage
php artisan storage:link --quiet || true

# Optimize caches for production performance
echo "Caching Laravel configuration, routes, and views..."
php artisan package:discover --ansi || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Fix permissions
echo "Adjusting file permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod 664 "$DB_FILE" || true

echo "=========================================================="
echo " QUICKSAVE AGENCIES LTD ERP is ready on port 8005"
echo " Access URL: http://localhost:8005"
echo "=========================================================="

exec "$@"
