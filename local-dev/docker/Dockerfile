FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y curl vim git zip unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer && chmod +x /usr/local/bin/composer

COPY local-dev/certs/auth.isklad.local/cert.crt /usr/local/share/ca-certificates/auth.crt
COPY local-dev/certs/myorder.isklad.local/cert.crt /usr/local/share/ca-certificates/myorder.crt
COPY local-dev/certs/egon.isklad.local/cert.crt /usr/local/share/ca-certificates/egon.crt

RUN update-ca-certificates

# Set working directory
WORKDIR /var/www/html
