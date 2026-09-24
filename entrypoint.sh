#!/bin/sh

# Limpiar cache de configuracion y rutas al iniciar el contenedor
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Publicar assets de Filament y vincular storage
php artisan filament:assets
php artisan storage:link --force

# Ejecutar migraciones y seeders en producción
php artisan migrate --force --seed

# Iniciar el servidor web (ajusta según cómo arranques tu app)
php artisan serve --host=0.0.0.0 --port=10000
