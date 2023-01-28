FROM php:8.1-fpm-alpine

RUN apk update
RUN apk add icu-dev

RUN docker-php-ext-install intl
RUN apk add libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql

RUN apk add zip libzip-dev
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

RUN apk add $PHPIZE_DEPS
RUN pecl install apcu
RUN docker-php-ext-enable apcu

RUN apk add --update linux-headers

RUN pecl install uopz
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN docker-php-ext-install pcntl sockets opcache

RUN apk add libpng-dev libjpeg-turbo-dev libwebp-dev
RUN docker-php-ext-configure gd --with-jpeg --with-webp
RUN docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apk add bash

WORKDIR /var/www/app

COPY ./docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod 777 /docker-entrypoint.sh

ENTRYPOINT ["/docker-entrypoint.sh"]