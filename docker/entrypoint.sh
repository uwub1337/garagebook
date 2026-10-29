#!/bin/sh
set -e

# 切換到專案目錄
cd /app

# 確保 storage 與 bootstrap/cache 目錄存在且具備寫入權限
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 確保 .env 檔案存在（避免 key:generate 找不到檔案而報錯）
if [ ! -f /app/.env ]; then
    if [ -f /app/.env.example ]; then
        cp /app/.env.example /app/.env
    else
        touch /app/.env
    fi
fi

# 如果未特別指定資料庫類型，預設使用 SQLite
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    export DB_CONNECTION=sqlite
    mkdir -p database
    touch database/database.sqlite
    chmod -R 775 database
fi

# 檢查與生成 APP_KEY
if [ -n "$APP_KEY" ]; then
    echo "Using provided APP_KEY."
    # 將環境變數中的 APP_KEY 寫入或更新到 .env
    if grep -q "^APP_KEY=" /app/.env; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" /app/.env
    else
        echo "APP_KEY=${APP_KEY}" >> /app/.env
    fi
else
    echo "Notice: APP_KEY is not set. Generating key..."
    php artisan key:generate --force
fi

# 執行資料庫遷移
echo "Running database migrations..."
php artisan migrate --force || echo "Migration warning: could not run migrations immediately."

# 預覽環境自動建立示範帳號、車輛與紀錄；正式環境請保持 PREVIEW_MODE=false。
if [ "$PREVIEW_MODE" = "true" ]; then
    echo "Seeding preview data..."
    php artisan db:seed --force
fi

# 建立 Storage 捷徑
php artisan storage:link || true

# 快取配置與路由以提升效能
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "Starting GarageBook on port ${PORT:-10000}..."
exec /usr/local/bin/frankenphp run --config /etc/caddy/Caddyfile
