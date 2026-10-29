#!/bin/sh
set -e

# 切換到專案目錄
cd /app

# 確保 storage 與 bootstrap/cache 目錄存在且具備寫入權限
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

# 檢查 APP_KEY
if [ -z "$APP_KEY" ]; then
    echo "Notice: APP_KEY is not set. Generating temporary key..."
    php artisan key:generate --force
fi

# 如果未特別指定資料庫類型，預設使用 SQLite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    export DB_CONNECTION=sqlite
    mkdir -p database
    touch database/database.sqlite
    chmod 777 database database/database.sqlite
fi

# 執行資料庫遷移
echo "Running migrations..."
php artisan migrate --force || echo "Migration warning: could not run migrations immediately."

# 建立 Storage 捷徑
php artisan storage:link || true

# 快取配置與路由以提升效能
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "Starting GarageBook on port ${PORT:-10000}..."
exec frankenphp run --config /etc/caddy/Caddyfile
