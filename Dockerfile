FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_pgsql \
    pdo_mysql \
    gd \
    mbstring \
    xml \
    zip \
    bcmath \
    opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock package.json package-lock.json ./

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction \
    && npm install --no-audit --no-fund

COPY . .

RUN npm run build \
    && composer dump-autoload --optimize --no-dev \
    && php artisan route:cache \
    && php artisan event:cache

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/sites-available/default

RUN mkdir -p /var/log/nginx \
    && mkdir -p /var/run/php

COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

EXPOSE 80

CMD ["/usr/local/bin/start"]
