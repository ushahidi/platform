FROM ushahidi/php-fpm-nginx:php-5.6

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader

COPY . .
RUN chgrp -R 0 . && chmod -R g+rwX . && \
	usermod -g 0 www-data && \
	chmod 777 application/cache application/media/uploads application/logs

COPY docker/run.nginx.conf /etc/nginx/sites-available/platform
RUN sed -i 's/$HTTP_PORT/'$HTTP_PORT'/' /etc/nginx/sites-available/platform && \
	rm /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/platform /etc/nginx/sites-enabled/default

COPY docker/common.sh /common.sh
COPY docker/run.run.sh /run.run.sh
COPY docker/run.tasks.conf /etc/chaperone.d/

ENV ENABLE_PLATFORM_TASKS=true \
    RUN_PLATFORM_MIGRATIONS=true

ENTRYPOINT [ "/bin/bash", "/run.run.sh" ]
