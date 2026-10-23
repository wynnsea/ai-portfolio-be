# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# base: PHP-FPM + extensions + Composer
# ---------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS base

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql intl zip bcmath opcache pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html

# ---------------------------------------------------------------------------
# app: production image (php-fpm on port 9000, also used by the queue worker)
# ---------------------------------------------------------------------------
FROM base AS app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi \
    && mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint && chmod +x /usr/local/bin/entrypoint

USER www-data
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

# ---------------------------------------------------------------------------
# test: app + dev dependencies, runs the PHPUnit suite on SQLite in memory
# ---------------------------------------------------------------------------
FROM app AS test

USER root
# gd is only needed by UploadedFile::fake()->image() in the tests
RUN install-php-extensions gd \
    && composer install --prefer-dist --no-interaction --no-scripts \
    && php artisan package:discover --ansi
ENTRYPOINT []
CMD ["php", "artisan", "test"]

# ---------------------------------------------------------------------------
# web: nginx serving public/ and uploaded files, proxying PHP to app:9000
# ---------------------------------------------------------------------------
FROM nginx:1.27-alpine AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
RUN ln -s /var/www/html/storage/app/public /var/www/html/public/storage
