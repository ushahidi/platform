FROM ushahidi/php-fpm-nginx:php-5.6

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader

COPY . .
RUN chgrp -R 0 . && chmod -R g+rwX .

COPY docker/run.nginx.conf /etc/nginx/sites-available/platform
RUN sed -i 's/$HTTP_PORT/'$HTTP_PORT'/' /etc/nginx/sites-available/platform && \
	rm /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/platform /etc/nginx/sites-enabled/default

COPY docker/common.sh /common.sh
COPY docker/run.run.sh /run.run.sh

ENTRYPOINT [ "/bin/bash", "/run.run.sh" ]
