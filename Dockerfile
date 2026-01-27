FROM php:8.2-fpm

RUN apt-get update && apt-get install -y nginx \
 && rm -rf /var/lib/apt/lists/*

ENV PORT=8080

COPY . /var/www/html
COPY nginx.conf /etc/nginx/nginx.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

CMD service nginx start && php-fpm
