FROM php:8.3-apache
RUN apt-get update && apt-get install -y libzip-dev zip unzip && docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
