#!/bin/bash

. /common.sh

set -e

sync
run_composer_install
cp .env.testing .env
wait_for_mysql
composer pre-test
(cd httpdocs/; php -S localhost:8000 -t . index.php &)

exec $*
