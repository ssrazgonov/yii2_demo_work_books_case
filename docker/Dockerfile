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

RUN useradd -u 1000 appuser && mkdir -p /var/www/html && chown -R 1000:1000 /var/www/html

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]