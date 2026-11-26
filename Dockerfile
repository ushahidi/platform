# Deployed images build on php-7.4; docker-compose.yml builds local images on
# php-7.4-debug, which adds Xdebug (make debug, make test with coverage).
ARG BASE_TAG=php-7.4
FROM ushahidi/php-fpm-nginx:${BASE_TAG}
LABEL org.opencontainers.image.source="https://github.com/ushahidi/platform"

# crond runs on chaperone, whose scheduler aiocron 2.x rejects the
# seconds-based intervals in docker/run.tasks.conf; keep it below 2.
RUN pip3 install --no-cache-dir 'aiocron<2'

# TODO: non-root user container setup
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY docker-php-ext-enable /usr/local/bin/
# Remote-debugging settings for PHP-FPM, only where the base image has Xdebug.
COPY docker-php-ext-xdebug.ini /tmp/
RUN if php -m | grep -qx xdebug; then \
      cp /tmp/docker-php-ext-xdebug.ini /etc/php/${PHP_MAJOR_VERSION}/fpm/conf.d/; \
    fi && rm /tmp/docker-php-ext-xdebug.ini
ENV PHP_INI_DIR=/etc/php/${PHP_MAJOR_VERSION}/cli

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
# symm/gisconverter is a path-repository package (its upstream was deleted); its
# source must be in the build context before composer install can resolve it.
COPY packages ./packages
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
