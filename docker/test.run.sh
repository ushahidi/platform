#!/bin/bash

. /common.sh

set -e

sync
run_composer_install
cp .env.testing .env
wait_for_mysql
bin/phinx migrate -c application/phinx.php
php -S localhost:8000 -t httpdocs httpdocs/index.php &

test_reporter() {
  local _ret=0;
  "$@" || _ret=$?
  if [ $_ret -ne 0 ]; then
    echo -e "\n\n* Test run failed, output of logs in application/logs follows:"
    echo -e "-------------------- BEGIN LOG OUTPUT --------------------"
    { find application/logs -type f -a \! -name .gitignore | sort ; echo "/dev/null"; } | xargs cat
    echo -e "--------------------- END LOG OUTPUT ---------------------"
    return 1
  else
    echo -e "\n* Successful test run"
    return 0
  fi
}

case "$1" in
  test_reporter)
    shift
    test_reporter "$@"
    ;;
  *)
    exec "$@"
    ;;
esac
