FROM php:8.2-apache

# 🔥 BORRAR cualquier MPM extra de raíz
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.conf

# Asegurar prefork
RUN a2enmod mpm_prefork rewrite

# Copiar proyecto
COPY . /var/www/html

# DocumentRoot a Laminas
RUN sed -i 's|/var/www/html|/var/www/html/app/public|g' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html
