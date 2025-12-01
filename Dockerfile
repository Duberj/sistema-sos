FROM php:8.1-apache

# Habilitar extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite si usas URLs amigables
RUN a2enmod rewrite

# Copiar app al servidor
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html

