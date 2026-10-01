# ---------- PHP base ----------
FROM php:8.4-fpm AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libzip-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql gd mbstring zip intl bcmath exif pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ---------- Dev: код монтируется volume'ом, зависимости ставятся при старте ----------
FROM base AS dev

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/php.ini "$PHP_INI_DIR/conf.d/app.ini"

CMD ["php-fpm"]

# ---------- Composer deps (нужны и для vite-сборки: ziggy импортируется из vendor) ----------
FROM base AS vendor

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --prefer-dist

# ---------- Frontend build (Inertia + Vue + Vite) ----------
FROM node:22-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY . .
COPY --from=vendor /var/www/html/vendor /app/vendor
RUN npm run build

# ---------- Prod: nginx + php-fpm в одном контейнере ----------
FROM base AS prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && docker-php-ext-install opcache \
    && apt-get update && apt-get install -y --no-install-recommends nginx supervisor \
    && rm -rf /var/lib/apt/lists/*

COPY --chown=www-data:www-data . /var/www/html
COPY --from=vendor --chown=www-data:www-data /var/www/html/vendor /var/www/html/vendor

RUN mkdir -p /var/www/main/storage/app/public \
    && ln -sfn /var/www/main/storage/app/public /var/www/html/public/storage

COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build

COPY docker/nginx.conf /etc/nginx/sites-enabled/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/app.conf
COPY docker/php.ini "$PHP_INI_DIR/conf.d/app.ini"
COPY docker/php-fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
