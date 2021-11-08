FROM php:7.4-fpm
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo_mysql
RUN apt-get update && apt-get install openssl libssl-dev libcurl4-openssl-dev -y

RUN pecl install mongodb
RUN docker-php-ext-enable mongodb
RUN echo "extension=mongo.so" > /usr/local/etc/php/conf.d/mongo.ini

WORKDIR /home/web/app

EXPOSE 9000
