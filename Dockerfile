# ==========================================
# ?挾 1嚗楊霅?Vue 3 ?垢
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /build

ARG VITE_PREVIEW_MODE=true
ENV VITE_PREVIEW_MODE=$VITE_PREVIEW_MODE

COPY frontend/package*.json ./
RUN npm ci

COPY frontend/ ./
RUN npm run build

# ==========================================
# ?挾 2嚗?鋆?Laravel 敺垢 Composer 憟辣
# ==========================================
FROM composer:2 AS backend-builder
WORKDIR /build

COPY backend/composer*.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

COPY backend/ ./
RUN composer dump-autoload --optimize --no-dev

# ==========================================
# ?挾 3嚗?蝯?銵憓?(FrankenPHP + PHP 8.4)
# ==========================================
FROM dunglas/frankenphp:1-php8.4-alpine

# 摰?撣貊??Laravel 敹??游?
RUN install-php-extensions \
    pdo_mysql \
    pdo_sqlite \
    bcmath \
    pcntl \
    intl \
    zip \
    opcache

# Render 的容器限制不允許 FrankenPHP 使用內建 capabilities。
RUN apk add --no-cache libcap \
    && setcap -r /usr/local/bin/frankenphp

WORKDIR /app

# 銴ˊ敺垢?訾?憟辣??撘Ⅳ
COPY --from=backend-builder /build /app

# 撠楊霅臬????垢??瑼?鋆賢 Laravel ??public ?桅?
COPY --from=frontend-builder /build/dist /app/public

# 銴ˊ Caddyfile ?????
COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh /usr/local/bin/frankenphp

# 蝣箔?甈?甇?Ⅱ
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 10000

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV PORT=10000

ENTRYPOINT ["docker-entrypoint.sh"]
