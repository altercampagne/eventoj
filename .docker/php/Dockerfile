FROM php:8.3-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev libpq-dev libxslt-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_pgsql xsl \
    && pecl install apcu \
    && docker-php-ext-enable apcu

COPY custom-config.ini /usr/local/etc/php/conf.d/custom-config.ini

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
