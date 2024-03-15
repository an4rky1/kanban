FROM serversideup/php:8.3-fpm-nginx

ENV PHP_OPCACHE_ENABLE=1

COPY --chown=www-data:www-data . /var/www/html

RUN composer install --optimize-autoloader --no-dev

RUN touch /var/www/html/storage/app/database.sqlite && \
    chown www-data:www-data /var/www/html/storage/app/database.sqlite && \
    chmod 664 /var/www/html/storage/app/database.sqlite

RUN php artisan optimize:clear && \
    php artisan view:cache && \
    php artisan route:cache && \
    php artisan config:cache

EXPOSE 80 443

HEALTHCHECK --interval=30s --timeout=3s CMD curl -f http://localhost:8080/up || exit 1
