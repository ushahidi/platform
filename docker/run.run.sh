#!/bin/bash

. /common.sh

set -e

run_composer_install

wait_for_mysql

if [ "${RUN_PLATFORM_MIGRATIONS}" == "true" ]; then
	run_migrations
else
	echo 'Waiting for database migrations to be done'
	while check_migrations_pending; do
		echo -n '.'
		sleep 5
	done
	echo
fi

bin/phinx migrate -c application/phinx.php

exec $*
