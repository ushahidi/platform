FROM ushahidi/php-fpm-nginx:php-5.6

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader

COPY . .
RUN chgrp -R 0 . && chmod -R g+rwX . && \
	usermod -g 0 www-data && \
	chmod 777 application/cache application/media/uploads application/logs

COPY docker/common.sh /common.sh
COPY docker/run.tasks.conf /etc/chaperone.d/

COPY docker/run.run.sh /run.run.sh
RUN $DOCKERCES_MANAGE_UTIL add /run.run.sh

ENV ENABLE_PLATFORM_TASKS=true \
    RUN_PLATFORM_MIGRATIONS=true \
		VHOST_ROOT=/var/www/httpdocs \
		VHOST_INDEX=index.php
