#!/bin/bash

. /common.sh

set -e

run_composer_install
wait_for_mysql
bin/phinx migrate -c application/phinx.php
php -S localhost:8000 -t httpdocs httpdocs/index.php &

exec $*
