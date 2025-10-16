FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data \
    && mkdir -p /var/www/html && chown -R www-data:www-data /var/www/html \
    && mkdir -p /var/www/html/web/uploads/covers \
    && chown -R www-data:www-data /var/www/html/web/uploads \
    && chmod -R 755 /var/www/html/web/uploads

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]