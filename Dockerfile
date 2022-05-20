FROM php:7.2-cli

RUN apt-get update -y && apt-get install -y libmcrypt-dev libjpeg62-turbo-dev libpng-dev libxpm-dev  \
    libjpeg-dev libfreetype6-dev libzip-dev libicu-dev
RUN apt-get update && \
    apt-get install -y zlib1g-dev && \
    apt-get install unzip

RUN apt update && apt install -y git
RUN docker-php-ext-install pdo mbstring pdo_mysql zip && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl
RUN docker-php-ext-configure gd --with-gd --with-jpeg-dir \
    --with-png-dir --with-zlib-dir --with-xpm-dir --with-freetype-dir
RUN docker-php-ext-install gd
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . /app

RUN composer install

EXPOSE 8000
CMD ["/app/symfony", "serve"]