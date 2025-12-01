FROM php:8.1-apache

# Extensiones PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite

# Puerto dinámico para Railway
ENV PORT=8080
EXPOSE 8080

# Copiar app
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# Copiar script para configurar Apache dinámicamente
COPY apache-port.sh /usr/local/bin/apache-port.sh
RUN chmod +x /usr/local/bin/apache-port.sh

# Iniciar Apache usando el script
CMD ["/usr/local/bin/apache-port.sh"]



