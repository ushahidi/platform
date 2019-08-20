FROM ushahidi/php-fpm-nginx:php-7.0

WORKDIR /var/www
COPY composer.json ./
COPY composer.lock ./
RUN composer install --no-autoloader --no-scripts

COPY . .
RUN chgrp -R 0 . && chmod -R g+rwX . && \
	usermod -g 0 www-data && \
	chmod 777 storage

COPY docker/common.sh /common.sh
COPY docker/run.tasks.conf /etc/chaperone.d/

COPY docker/run.run.sh /run.run.sh
RUN $DOCKERCES_MANAGE_UTIL add /run.run.sh

ENV ENABLE_PLATFORM_TASKS=true \
    RUN_PLATFORM_MIGRATIONS=true \
    VHOST_ROOT=/var/www/httpdocs \
    VHOST_INDEX=index.php \
    PHP_EXEC_TIME_LIMIT=3600

ENTRYPOINT []

USER root

# Install MySQL
RUN apt-get update \
 && apt-get install -y \
    mysql-server \
    sudo \
 && apt-get clean && rm -rf /var/cache/apt/* /var/lib/apt/lists/* /tmp/* \
 && mkdir /var/run/mysqld

# Add Gitpod user explicitly, as we are not inheriting from a Gitpod base image
RUN useradd -l -u 33333 -G sudo -md /home/gitpod -s /bin/bash -p gitpod gitpod \
    # passwordless sudo for users in the 'sudo' group
    && sed -i.bkp -e 's/%sudo\s\+ALL=(ALL\(:ALL\)\?)\s\+ALL/%sudo ALL=NOPASSWD:ALL/g' /etc/sudoers

RUN chown -R gitpod:gitpod /etc/mysql /var/run/mysqld /var/log/mysql /var/lib/mysql /var/lib/mysql-files /var/lib/mysql-keyring /var/lib/mysql-upgrade || true

# Install our own MySQL config
COPY .gitpod/mysql.cnf /etc/mysql/mysql.conf.d/mysqld.cnf

# Install default-login for MySQL clients
COPY .gitpod/client.cnf /etc/mysql/mysql.conf.d/client.cnf

USER gitpod
