FROM ushahidi/php-fpm-nginx:php-7.4
LABEL org.opencontainers.image.source="https://github.com/ushahidi/platform"

# TODO: non-root user container setup
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY docker-php-ext-enable /usr/local/bin/
RUN apt-get update 
RUN apt-get install -y php-pear php${PHP_MAJOR_VERSION}-dev
RUN pecl channel-update pecl.php.net
RUN pecl channel-update pecl.php.net
RUN pecl install xdebug-3.1.6 
ENV PHP_INI_DIR=/etc/php/${PHP_MAJOR_VERSION}/fpm
RUN docker-php-ext-enable xdebug
ENV PHP_INI_DIR=/etc/php/${PHP_MAJOR_VERSION}/cli
RUN docker-php-ext-enable xdebug
COPY docker-php-ext-xdebug.ini /etc/php/${PHP_MAJOR_VERSION}/fpm/conf.d

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer self-update --2
RUN composer install --no-autoloader --no-scripts

COPY . .
COPY docker/utils.sh /utils.sh
COPY docker/run.tasks.conf /etc/chaperone.d/
COPY docker/run.run.sh /run.run.sh
RUN echo '#!/bin/bash\n. /utils.sh\n"$@"' > /bin/util ; chmod +x /bin/util ;

RUN $DOCKERCES_MANAGE_UTIL add /run.run.sh

ARG GIT_COMMIT_ID
ARG GIT_BUILD_REF

ENV ENABLE_PLATFORM_TASKS=true \
    DB_MIGRATIONS_HANDLED=true \
    RUN_PLATFORM_MIGRATIONS=true \
    VHOST_ROOT=/var/www/httpdocs \
    VHOST_INDEX=index.php \
    PHP_EXEC_TIME_LIMIT=3600 \
    GIT_COMMIT_ID=${GIT_COMMIT_ID} \
    GIT_BUILD_REF=${GIT_BUILD_REF}

CMD [ "start" ]
