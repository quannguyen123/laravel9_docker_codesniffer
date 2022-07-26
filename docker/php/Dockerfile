ARG PHP_VERSION=latest
FROM php:${PHP_VERSION}-fpm

LABEL maintainer="Do Quyet Tien <tiendq@hybrid-technologies.co.jp>"

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y \
    sudo \
    vim \
    git \
    curl \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libmcrypt-dev \
    libgmp-dev \
    libzip-dev zip unzip && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip && \
    php -m | grep -q 'zip' \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install gmp \
    && docker-php-ext-install exif \
    && docker-php-ext-install mysqli \
    && rm -rf /var/lib/apt/lists/*

###########################################################################
# Set Timezone
###########################################################################

ARG TZ=UTC
ENV TZ ${TZ}

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

###########################################################################
# PHP extension
###########################################################################

# Redis:

RUN pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

# Opcache:

RUN docker-php-ext-install opcache

# Copy opcache configration
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini

###########################################################################
# NodeJS
###########################################################################

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - && \
    apt-get -y install nodejs

RUN node -v && npm -v

###########################################################################
# Composer
###########################################################################

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

###########################################################################
# Check PHP version:
###########################################################################

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${PHP_VERSION}."

#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

COPY ./laravel.ini /usr/local/etc/php/conf.d
COPY ./php.ini /usr/local/etc/php/php.ini

USER root

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

RUN usermod -u 1000 www-data

ARG WORKDIR="/usr/share/nginx/html"

WORKDIR ${WORKDIR}

CMD ["php-fpm"]

EXPOSE 9000
