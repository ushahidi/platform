FROM ushahidi/php-ci:php-5.6.30

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader

COPY docker/build.run.sh /build.run.sh

ENTRYPOINT [ "/bin/bash", "/build.run.sh" ]
