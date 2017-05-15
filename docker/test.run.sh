#!/bin/bash
set -e

check_vols_src() {
  if [ ! -d /vols/src ]; then
    echo "No /vols/src with code"
    exit 1
  fi
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
  until nc -z mysql 3306; do
    >&2 echo "Mysql is unavailable - sleeping"
    sleep 1
  done
}

sync
run_composer_install
cp .env.testing .env
wait_for_mysql
composer pre-test
php -S localhost:8000 -t httpdocs httpdocs/index.php &

exec $*
