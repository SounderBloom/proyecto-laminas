FROM php:8.2-apache

# Habilitar rewrite
RUN a2enmod rewrite

# Copiar proyecto
COPY . /var/www/html

# DocumentRoot a public
RUN sed -i 's|/var/www/html|/var/www/html/app/public|g' \
    /etc/apache2/sites-available/000-default.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html
