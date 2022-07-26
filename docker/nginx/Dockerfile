ARG NGINX_VERSION=latest
FROM nginx:${NGINX_VERSION}

LABEL maintainer="Do Quyet Tien <tiendq@hybrid-technologies.co.jp>"

ARG PHP_UPSTREAM_CONTAINER=php
ARG PHP_UPSTREAM_PORT=9000

RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y openssl bash vim curl \
    && adduser -H -u 1000 -s /bin/bash www-data

###########################################################################
# Set Timezone
###########################################################################

ARG TZ=UTC
ENV TZ ${TZ}

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Set upstream conf and remove the default conf
RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf \
    && rm /etc/nginx/conf.d/default.conf

COPY ./nginx.conf /etc/nginx/
COPY ./fastcgi.conf /etc/nginx/

CMD ["nginx"]

EXPOSE 80 443
