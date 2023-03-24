#!/bin/bash

## "noop" command -- holds the execution so nothing gets done
if [ "${@: -1}" == "noop" ]; then
  sleep infinity
  exit 0
fi

## Perform container initialisation

. $(dirname $0)/utils.sh

set -e

copy_external_config
touch_logs

# Dump lumen disk logs if something fails
trap dump_logs EXIT

run_composer_install --no-dev --no-scripts
run_composer dumpautoload
provision_passport_keys
set_storage_permissions

# Not all setups require containers handling migrations (i.e. multisite)
if [ "${DB_MIGRATIONS_HANDLED}" == "true" ]; then
# Not all containers may need to run migrations
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
fi

# Show logs so far , untrap exit
trap - EXIT
dump_logs

# Mark bootstrap complete
bootstrap_done

exec "$@"
