FROM php:8.1-apache

# Extensiones PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

# Puerto dinámico para Railway
ENV PORT=8080
EXPOSE 8080

# Configurar Apache para escuchar en el puerto correcto
COPY apache-port.conf /etc/apache2/sites-enabled/000-default.conf

# Copiar app
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

CMD ["apache2-foreground"]


