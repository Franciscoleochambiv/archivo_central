FROM php:8.2-fpm

# Instala dependencias del sistema y la extensión redis
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    nano \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo compatible con NGINX
WORKDIR /var/www/html

# Copia el código de la aplicación
COPY . .

# Instala dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev --prefer-dist

# Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Expone el puerto estándar de php-fpm
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]
