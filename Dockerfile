# Shirobell production images (see docs/operations/docker-deploy.md).
#   target "app": PHP-FPM application, also used for the queue worker and scheduler
#   target "web": Nginx serving public/ and passing PHP to the app container

# ---- Composer dependencies -------------------------------------------------
FROM composer:2.8 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs

# ---- Frontend assets ---------------------------------------------------------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
# Tailwind scans these Blade views from vendor (resources/css/app.css @source).
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build

# ---- Application (PHP-FPM) ---------------------------------------------------
FROM php:8.3-fpm-alpine AS app
WORKDIR /var/www/html
# Lets the deploy job clean up only its own old images on a shared VPS.
LABEL io.shirobell.app="shirobell"

# install-php-extensions handles build deps and GD with WebP.
COPY --from=mlocati/php-extension-installer:2.7 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions bcmath gd intl pdo_mysql zip opcache pcntl exif \
    && apk add --no-cache fcgi

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-shirobell.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-shirobell.conf
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize --classmap-authoritative \
    && php artisan package:discover --ansi \
    && php artisan filament:assets --no-interaction \
    && rm -f public/hot \
    && ln -sfn ../storage/app/public public/storage \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/shirobell-entrypoint
RUN chmod +x /usr/local/bin/shirobell-entrypoint

USER www-data
ENTRYPOINT ["shirobell-entrypoint"]
CMD ["php-fpm"]

# ---- Web server (Nginx) ------------------------------------------------------
FROM nginx:1.27-alpine AS web
WORKDIR /var/www/html
LABEL io.shirobell.app="shirobell"
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public ./public
