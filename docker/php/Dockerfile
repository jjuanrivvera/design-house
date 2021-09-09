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

# Copy helpers binaries
RUN mkdir /tmp/bin
COPY /bin/* /tmp/bin/
RUN chmod +x /tmp/bin/*
RUN mv /tmp/bin/* /bin/

# Expose port 9000 and start php-fpm server
EXPOSE 9000
