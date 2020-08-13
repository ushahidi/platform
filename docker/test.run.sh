#!/bin/bash

. /common.sh

set -e

sync
cp .env.testing .env
run_composer_install
wait_for_mysql
composer pre-test
(cd httpdocs/; php -S localhost:8000 -t . index.php &)

test_reporter() {
  local _ret=0;
  "$@" || _ret=$?
  if [ $_ret -ne 0 ]; then
    echo -e "\n\n* Test run failed, output of logs in application/logs follows:"
    echo -e "-------------------- BEGIN LOG OUTPUT --------------------"
    cat storage/logs/lumen.log
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
