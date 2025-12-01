#!/bin/bash
# Reemplaza el puerto en la conf de Apache
sed -i "s/Listen 8080/Listen ${PORT}/" /etc/apache2/sites-enabled/000-default.conf
sed -i "s/<VirtualHost \*:8080>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-enabled/000-default.conf

# Inicia Apache
apache2-foreground
