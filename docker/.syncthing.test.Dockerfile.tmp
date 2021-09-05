ARG PHP_MAJOR_VERSION
FROM ushahidi/php-ci:php-${PHP_MAJOR_VERSION}

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader --no-scripts

COPY docker/common.sh /common.sh
COPY docker/test.run.sh /test.run.sh

ENTRYPOINT [ "/bin/bash", "/test.run.sh" ]
