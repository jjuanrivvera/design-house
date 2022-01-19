FROM php:7.2-fpm-alpine

# Set working directory
WORKDIR /var/www

RUN docker-php-ext-install pdo pdo_mysql && \
    apk add bash git gd

# imagick
RUN apk add --update --no-cache --virtual .deps autoconf g++ imagemagick imagemagick-dev libtool make pcre-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apk del autoconf g++ libtool make pcre-dev

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apk update \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

ARG XDEBUG_CLIENT_HOST

RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_startup_errors = Off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_errors = Off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug,develop,trace,profile" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=${XDEBUG_CLIENT_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.connect_timeout_ms=300" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && cat /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    sed -i -e "s/^ *memory_limit.*/memory_limit = 4G/g" -e "s/^ *max_execution_time.*/max_execution_time = 0/g" /usr/local/etc/php/php.ini

# Expose port 9000 and start php-fpm server
EXPOSE 9000
