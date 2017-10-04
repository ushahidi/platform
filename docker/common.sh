#!/bin/bash
set -e

check_vols_src() {
  if [ ! -d /vols/src ]; then
    echo "No /vols/src with code"
    exit 1
  fi
}

check_migrations_pending() {
  local n_pending=$(./bin/phinx status --no-ansi -c application/phinx.php | grep -E '^[[:space:]]+down[[:space:]]+' | wc -l)
  [ $n_pending -gt 0 ]
}

run_migrations() {
  ./bin/phinx migrate -c application/phinx.php
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
    echo "- tmp"
  } > /tmp/rsync_exclude
  rsync -ar --exclude-from=/tmp/rsync_exclude --delete-during /vols/src/ ./
  rm -f phpunit.xml behat.yml phpspec.yml
}

function run_composer_install {
  composer install --no-interaction
}

function wait_for_mysql {
  local db_host=${DB_HOST:-mysql}
  local db_port=${DB_PORT:-3306}
  until nc -z $db_host $db_port; do
    >&2 echo "Mysql is unavailable - sleeping"
    sleep 1
  done
}

