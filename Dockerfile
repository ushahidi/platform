FROM ushahidi/php-fpm-nginx:5.6

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader

COPY . .
RUN chown -R www-data:www-data application/cache application/media/uploads application/logs

COPY docker/run.nginx.conf /etc/nginx/sites-available/platform
RUN rm /etc/nginx/sites-enabled/default && \
    ln -s /etc/nginx/sites-available/platform /etc/nginx/sites-enabled/default

COPY docker/common.sh /common.sh
COPY docker/run.run.sh /run.run.sh

ENTRYPOINT [ "/bin/bash", "/run.run.sh" ]
