FROM php:8.1-apache

# Habilitar mysqli
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar los archivos del proyecto al servidor Apache
COPY . /var/www/html/

# Dar permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto
EXPOSE 80
