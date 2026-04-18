#!/bin/bash
set -e

echo "=== AUSHVERA Laravel Startup ==="

# ─── Create .env file from environment variables ─────────────────────────────
# Render injects env vars as system env, but Laravel needs a physical .env file
ENV_FILE="/var/www/html/.env"

echo "Creating .env file from environment variables..."
cat > "$ENV_FILE" <<EOF
APP_NAME=${APP_NAME:-AUSHVERA}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://thaaushvera.onrender.com}

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=${DB_CONNECTION:-sqlite}
DB_DATABASE=${DB_DATABASE:-/var/data/database.sqlite}

SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
CACHE_DRIVER=${CACHE_DRIVER:-file}
QUEUE_CONNECTION=sync

# AI Support
OPENAI_API_KEY=${OPENAI_API_KEY:-}
GEMINI_API_KEY=${GEMINI_API_KEY:-}
STABILITY_API_KEY=${STABILITY_API_KEY:-}
AI_SERVER_URL=${AI_SERVER_URL:-http://127.0.0.1:9000}

# Social Login Support
GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID:-}
GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET:-}
GOOGLE_REDIRECT_URI=${GOOGLE_REDIRECT_URI:-}
FACEBOOK_CLIENT_ID=${FACEBOOK_CLIENT_ID:-}
FACEBOOK_CLIENT_SECRET=${FACEBOOK_CLIENT_SECRET:-}
FACEBOOK_REDIRECT_URI=${FACEBOOK_REDIRECT_URI:-}

# Payment Support
RAZORPAY_KEY=${RAZORPAY_KEY:-}
RAZORPAY_SECRET=${RAZORPAY_SECRET:-}
RAZORPAY_WEBHOOK_SECRET=${RAZORPAY_WEBHOOK_SECRET:-}

MAIL_MAILER=log
BCRYPT_ROUNDS=12
EOF

echo ".env file created."

# ─── Generate APP_KEY if not set ─────────────────────────────────────────────
if ! grep -q "APP_KEY=base64:" "$ENV_FILE" || [ -z "$APP_KEY" ]; then
    echo "Generating or fixing APP_KEY..."
    php artisan key:generate --force
fi

# ─── Ensure SQLite database file exists ──────────────────────────────────────
DB_PATH=${DB_DATABASE:-/var/data/database.sqlite}
mkdir -p "$(dirname $DB_PATH)"
if [ ! -f "$DB_PATH" ]; then
    echo "Creating SQLite database at $DB_PATH..."
    touch "$DB_PATH"
fi
chmod 664 "$DB_PATH" 2>/dev/null || true
chown www-data:www-data "$DB_PATH" 2>/dev/null || true

# ─── Storage & cache permissions ─────────────────────────────────────────────
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/bootstrap/cache

chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# ─── Run migrations ──────────────────────────────────────────────────────────
echo "Running migrations..."
php artisan migrate --force --no-interaction 2>&1 || echo "Migration note: may already be up to date"

# ─── Seed super admin ─────────────────────────────────────────────────────────
echo "Seeding super admin..."
php artisan db:seed --class=SuperAdminSeeder --force 2>&1 || echo "Seeder note: super admin already exists"

# ─── Cache for performance ───────────────────────────────────────────────────
echo "Caching config and routes..."
php artisan config:cache 2>&1 || true
php artisan route:cache  2>&1 || true
php artisan view:cache   2>&1 || true

echo "=== Starting Apache ==="
exec apache2-foreground
