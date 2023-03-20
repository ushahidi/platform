#!/bin/bash

check_vols_src() {
  if [ ! -d /var/www ]; then
    echo "No /var/www with code"
    exit 1
  fi
}
check_vols_out() {
  if [ ! -d /var/out ]; then
    echo "No /var/out for output!"
    exit 1
  fi
}

# Bring in source files, taking care not to bring in useless files or overwriting useful ones
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
  rsync -ar --exclude-from=/tmp/rsync_exclude --delete-during /var/www/ ./
}

function run_composer_install {
  composer install --no-interaction --no-scripts
}

function bundle {
  check_vols_out
  local version=${GITHUB_VERSION:-${CI_BRANCH:-v0.0.0}}
  DEST_DIR=/var/out ./bin/release $version
}

sync
run_composer_install
bundle
