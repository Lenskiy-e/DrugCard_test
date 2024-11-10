FROM php:8.3-apache

VOLUME ["/var/www/html"]

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    git \
    zip

RUN docker-php-ext-install pdo pdo_mysql intl mbstring xml

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./virtualhost.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80