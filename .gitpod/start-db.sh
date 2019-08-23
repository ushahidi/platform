#!/bin/bash

# this script is intended to be called from .bashrc
# This is a workaround for not having something like supervisord

if [ ! -e /var/run/mysqld/gitpod-init.lock ]
then
    touch /var/run/mysqld/gitpod-init.lock

    # mysql 5.5
    # initialize database structures on disk, if needed
    if [ ! -d /workspace/mysql ]; then
        mkdir /workspace/mysql;
        mysql_install_db --defaults-file=/etc/mysql/mysql.conf.d/mysqld.cnf --user=gitpod --auth-root-authentication-method=normal
    fi

    # launch database, if not running
    [ ! -e /var/run/mysqld/mysqld.pid ] && mysqld --defaults-file=/etc/mysql/mysql.conf.d/mysqld.cnf &

    rm /var/run/mysqld/gitpod-init.lock
fi

# Ushahidi specific user setup
mysql -e 'grant all on ushahidi.* to `homestead`@`localhost` identified by "secret"; flush privileges;' -u root &&
mysql -e 'create database ushahidi;' -u root
