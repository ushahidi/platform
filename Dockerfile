FROM ushahidi/php-fpm-nginx:php-7.4

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

ENV ENABLE_PLATFORM_TASKS=true \
    DB_MIGRATIONS_HANDLED=true \
    RUN_PLATFORM_MIGRATIONS=true \
    VHOST_ROOT=/var/www/httpdocs \
    VHOST_INDEX=index.php \
    PHP_EXEC_TIME_LIMIT=3600

