FROM php:8.3-apache

# 🔥 LIMPIAR MPMs CONFLICTIVOS (ESTO ES LA CLAVE)
RUN rm -f /etc/apache2/mods-enabled/mpm_event.* \
          /etc/apache2/mods-enabled/mpm_worker.*

# Asegurar prefork
RUN a2enmod mpm_prefork

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-install zip intl pdo_pgsql

# Apache mods
RUN a2enmod rewrite

# Apache → Laminas public
RUN sed -i 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Composer
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

# Copiar proyecto
COPY . /var/www

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Railway
ENV PORT=8080
EXPOSE 8080

RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf \
 && sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf
