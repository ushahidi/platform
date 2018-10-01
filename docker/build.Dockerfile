FROM ushahidi/php-ci:php-7.0

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader --no-scripts

COPY docker/build.run.sh /build.run.sh

ENTRYPOINT [ "/bin/bash", "/build.run.sh" ]
