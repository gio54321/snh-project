FROM php:8.2-fpm

RUN apt update && apt install -y git procps
# install pdo for mysql
RUN docker-php-ext-install mysqli pdo pdo_mysql
