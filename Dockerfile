FROM php:8.2-fpm

# install pdo for mysql
RUN docker-php-ext-install mysqli pdo pdo_mysql
