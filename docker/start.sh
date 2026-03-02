#!/bin/bash
set -e

echo "=== AUSHVERA Laravel Startup ==="

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Set database path for persistent SQLite
export DB_DATABASE=${DB_DATABASE:-/var/data/database.sqlite}

# Ensure SQLite file exists
if [ ! -f "$DB_DATABASE" ]; then
    echo "Creating SQLite database at $DB_DATABASE..."
    mkdir -p "$(dirname $DB_DATABASE)"
    touch "$DB_DATABASE"
    chmod 664 "$DB_DATABASE"
    chown www-data:www-data "$DB_DATABASE"
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction 2>&1 || echo "Migration warning (may already exist)"

# Cache for performance
echo "Caching config and routes..."
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

echo "=== Starting Apache ==="
exec apache2-foreground
