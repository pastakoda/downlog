FROM php
RUN apt-get update && apt-get install -y git libzip-dev && docker-php-ext-install zip
RUN  curl https://getcomposer.org/installer  | php -- --install-dir=/usr/bin --filename=composer