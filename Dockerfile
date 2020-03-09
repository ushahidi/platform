FROM ushahidi/php-fpm-nginx:php-7.2

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader --no-scripts

COPY . .
COPY docker/common.sh /common.sh
COPY docker/run.tasks.conf /etc/chaperone.d/
COPY docker/run.run.sh /run.run.sh

RUN $DOCKERCES_MANAGE_UTIL add /run.run.sh

ENV ENABLE_PLATFORM_TASKS=true \
    RUN_PLATFORM_MIGRATIONS=true \
    VHOST_ROOT=/var/www/httpdocs \
    VHOST_INDEX=index.php \
    PHP_EXEC_TIME_LIMIT=3600

