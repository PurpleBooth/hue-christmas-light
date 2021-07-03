FROM php:7.3.29-cli
COPY . /usr/src/nini
WORKDIR /usr/src/nini
RUN apt-get update && apt-get install -y git zlib1g-dev && \
    docker-php-ext-install -j$(nproc) zip
RUN php -r "readfile('https://getcomposer.org/installer');" | php
RUN php composer.phar install
ENTRYPOINT [ "php", "./nini.php" ]