FROM php:8.2-apache

# Desactivar MPMs incorrectos y dejar solo prefork
RUN a2dismod mpm_event mpm_worker \
 && a2enmod mpm_prefork

# Habilitar mod_rewrite (muy común en Laminas)
RUN a2enmod rewrite

# Copiar el proyecto
COPY . /var/www/html

# Apuntar el DocumentRoot a /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html
