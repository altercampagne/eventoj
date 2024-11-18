FROM php:8.3-fpm

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions apcu intl opcache pdo pdo_pgsql xsl zip

COPY custom-config.ini /usr/local/etc/php/conf.d/custom-config.ini

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
