# ==========================================
# 階段 1：編譯 Vue 3 前端
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /build

COPY frontend/package*.json ./
RUN npm ci

COPY frontend/ ./
RUN npm run build

# ==========================================
# 階段 2：安裝 Laravel 後端 Composer 套件
# ==========================================
FROM composer:2 AS backend-builder
WORKDIR /build

COPY backend/composer*.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

COPY backend/ ./
RUN composer dump-autoload --optimize --no-dev

# ==========================================
# 階段 3：最終運行環境 (FrankenPHP + PHP 8.4)
# ==========================================
FROM dunglas/frankenphp:1-php8.4-alpine

# 安裝常用的 Laravel 必要擴充
RUN install-php-extensions \
    pdo_mysql \
    pdo_sqlite \
    bcmath \
    pcntl \
    intl \
    zip \
    opcache

WORKDIR /app

# 複製後端相依套件與程式碼
COPY --from=backend-builder /build /app

# 將編譯完成的前端靜態檔複製到 Laravel 的 public 目錄
COPY --from=frontend-builder /build/dist /app/public

# 複製 Caddyfile 與啟動腳本
COPY Caddyfile /etc/caddy/Caddyfile
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 確保權限正確
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 10000

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PORT=10000

ENTRYPOINT ["docker-entrypoint.sh"]
