FROM php:8.2-apache

# Habilitar rewrite (necesario para Laminas)
RUN a2enmod rewrite

# Copiar el proyecto
COPY . /var/www/html

# Apuntar Apache a /public
RUN sed -i 's|/var/www/html|/var/www/html/app/public|g' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess (IMPORTANTE)
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html
