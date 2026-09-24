FROM php:8.2-fpm

# Instalar dependencias del sistema necesarias (incluyendo PostgreSQL, ICU e intl)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    nginx

# Instalar extensiones de PHP (añadidas: pdo_pgsql, pgsql, intl y zip)
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_pgsql pgsql intl zip pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Configurar permisos de directorios
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=80

# Ejecutar comandos de optimización y assets de Filament
RUN php artisan filament:assets
RUN php artisan storage:link

# Copiar el script de entrada y darle permisos en Linux
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Establecer el script como entrypoint
ENTRYPOINT ["/entrypoint.sh"]