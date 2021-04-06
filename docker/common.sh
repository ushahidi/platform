#!/bin/bash
set -e

function copy_external_config() {
  if [ -n "$DOTENV_PATH" ] && [ -f "$DOTENV_PATH" ]; then
    if [ -f .env ]; then
      echo "NOTICE: replacing .env file with contents from $DOTENV_PATH"
    fi
    cat $DOTENV_PATH > .env
  fi
  if [ -n "$PASSPORT_KEYS_PATH" ] && [ -d "$PASSPORT_KEYS_PATH" ] && [ -e "$PASSPORT_KEYS_PATH/oauth-private.key" ] ; then
    if [ -e ./storage/passport/oauth-private.key ]; then
      echo "NOTICE: replacing passport key files with those from $PASSPORT_KEYS_PATH"
    fi
    cp -f $PASSPORT_KEYS_PATH/*.key ./storage/passport/
  fi
}

function check_vols_src() {
  if [ ! -d /vols/src ]; then
    echo "No /vols/src with code"
    exit 1
  fi
}

function check_migrations_pending() {
  local n_pending=$(./bin/phinx status --no-ansi -c phinx.php | grep -E '^[[:space:]]+down[[:space:]]+' | wc -l)
  [ $n_pending -gt 0 ]
}

function as_www_data() {
  su -s /bin/sh www-data -c "$*"
}

function run_migrations() {
  as_www_data composer migrate
}

function provision_passport_keys() {
  if [ ! -d storage/passport ]; then
    mkdir -p storage/passport
  fi
  if [ ! -f storage/passport/oauth-private.key ]; then
    composer bootstrap:passport
  fi
}

function touch_logs() {
  # Archive previous logs , start with new files for current run
  for f in lumen.log worker.log; do
    if [ -f "storage/logs/${f}" ]; then
      cat "storage/logs/${f}" >> storage/logs/${f}.archive
    fi
    truncate -s 0 storage/logs/${f}
  done
}

function dump_logs() {
  echo
  echo "---> [i] Dump of lumen logs"
  echo
  for f in lumen.log worker.log; do
    if [ -f "storage/logs/${f}" ] && [ `stat -c %s "storage/logs/${f}"` -gt 0 ]; then
      echo "---- ${f} ----"
      cat storage/logs/${f}
    fi
  done
  echo "---------------------------"
  echo
}

function set_storage_permissions() {
  chown -R www-data storage/
}

function sync {
  check_vols_src
  {
    for f in bin/*; do
      echo "- ${f}"
    done
    for f in modules/*; do
      echo "- ${f}"
    done
    echo "- .git"
    echo "- vendor"
    echo "- storage/app/public/**"
    echo "- storage/app/temp/**"
    echo "- storage/framework/cache/**"
    echo "- storage/framework/testing/**"
    echo "- storage/framework/views/**"
    echo "- storage/logs/**"
    echo "- storage/passport/**"
    echo "- tmp"
  } > /tmp/rsync_exclude
  rsync -ar --exclude-from=/tmp/rsync_exclude --delete-during /vols/src/ ./
  rm -f phpunit.xml behat.yml phpspec.yml
}

function run_composer_install {
  if [ ! -d storage/passport ]; then
    mkdir -p storage/passport
  fi
  composer install --no-interaction "$@"
  chmod 0600 storage/passport/*
}

function wait_for_mysql {
  local db_host=${DB_HOST}
  local db_port=${DB_PORT:-3306}
  until nc -z $db_host $db_port; do
    >&2 echo "Mysql ($db_host:$db_port) is unavailable - sleeping"
    sleep 1
  done
}
