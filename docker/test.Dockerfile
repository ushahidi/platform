FROM ushahidi/php-ci:php-7.0

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader --no-scripts

COPY docker/common.sh /common.sh
COPY docker/test.run.sh /test.run.sh

ENTRYPOINT [ "/bin/bash", "/test.run.sh" ]
