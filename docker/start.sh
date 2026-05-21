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
APP_URL=${APP_URL:-https://thaaaaaaushvera.onrender.com}

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DATABASE_URL=${DATABASE_URL:-}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
CACHE_DRIVER=database
QUEUE_CONNECTION=sync

# Social Login Support
GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID:-}
GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET:-}
GOOGLE_REDIRECT_URI=${GOOGLE_REDIRECT_URI:-https://thaaaaaaushvera.onrender.com/auth/google/callback}
FACEBOOK_CLIENT_ID=${FACEBOOK_CLIENT_ID:-}
FACEBOOK_CLIENT_SECRET=${FACEBOOK_CLIENT_SECRET:-}
FACEBOOK_REDIRECT_URI=${FACEBOOK_REDIRECT_URI:-https://thaaaaaaushvera.onrender.com/auth/facebook/callback}

# Payment Support
RAZORPAY_KEY=${RAZORPAY_KEY:-}
RAZORPAY_SECRET=${RAZORPAY_SECRET:-}
RAZORPAY_WEBHOOK_SECRET=${RAZORPAY_WEBHOOK_SECRET:-}

# Chatbot / AI
GEMINI_API_KEY=${GEMINI_API_KEY:-}

MAIL_MAILER=log
BCRYPT_ROUNDS=12
EOF

echo ".env file created."

# ─── Generate APP_KEY if not set ─────────────────────────────────────────────
if ! grep -q "APP_KEY=base64:" "$ENV_FILE" || [ -z "$APP_KEY" ]; then
    echo "Generating or fixing APP_KEY..."
    php artisan key:generate --force
fi

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

# ─── Seed sample products ─────────────────────────────────────────────────
echo "Seeding sample products..."
php artisan db:seed --class=ProductSeeder --force 2>&1 || echo "Seeder note: products may already exist"

# ─── Cache for performance ───────────────────────────────────────────────────
echo "Clearing old caches..."
php artisan config:clear 2>&1 || true
php artisan route:clear  2>&1 || true
php artisan view:clear   2>&1 || true

echo "Caching config and views..."
php artisan config:cache 2>&1 || true
php artisan view:cache   2>&1 || true
# Note: route:cache is skipped if closures exist in routes files
php artisan route:cache  2>&1 || echo "Route caching skipped (closures in routes)"

# ─── Keep-Alive Background Ping (Prevents Render Free Tier Sleep) ─────────
echo "Starting keep-alive background ping..."
(
    while true; do
        sleep 840  # 14 minutes
        curl -s -o /dev/null -w "%{http_code}" "${APP_URL:-https://thaaaaaaushvera.onrender.com}/api/health" || true
        echo "[keep-alive] Pinged at $(date)"
    done
) &
KEEPALIVE_PID=$!
echo "Keep-alive started (PID: $KEEPALIVE_PID)"

echo "=== Starting Apache ==="
exec apache2-foreground
