# Official PHP image, pinned to the patched release for CVE-2026-109618/109619.
# Bump this tag for future PHP security releases.
FROM php:8.3.36-apache

# cal_days_in_month() needs the calendar extension
RUN docker-php-ext-install calendar \
 && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Coolify routes to port 3000 (matches the previous Nixpacks build)
RUN sed -i 's/^Listen 80$/Listen 3000/' /etc/apache2/ports.conf \
 && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:3000>/' /etc/apache2/sites-available/000-default.conf

COPY index.php /var/www/html/index.php

EXPOSE 3000
