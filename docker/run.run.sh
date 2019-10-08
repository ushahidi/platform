#!/bin/bash

## "noop" command -- holds the execution so nothing gets done
if [ "${@: -1}" == "noop" ]; then
  sleep infinity
  exit 0
fi

## Perform container initialisation

. /common.sh

set -e

run_composer_install
provision_passport_keys
set_storage_permissions

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

exec "$@"
