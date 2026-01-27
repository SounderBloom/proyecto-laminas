FROM php:8.2-apache

# 🔥 Forzar prefork (el único compatible con mod_php)
RUN a2dismod mpm_event mpm_worker \
 && a2enmod mpm_prefork rewrite \
 && sed -i 's/^LoadModule mpm_event_module/#LoadModule mpm_event_module/' /etc/apache2/apache2.conf \
 && sed -i 's/^LoadModule mpm_worker_module/#LoadModule mpm_worker_module/' /etc/apache2/apache2.conf

# Railway usa el puerto 8080
ENV PORT=8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf \
 && sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf

# Copiar proyecto
COPY . /var/www/html

# DocumentRoot Laminas
RUN sed -i 's|/var/www/html|/var/www/html/app/public|g' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html
