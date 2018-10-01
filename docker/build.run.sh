#!/bin/bash

check_vols_src() {
  if [ ! -d /vols/src ]; then
    echo "No /vols/src with code"
    exit 1
  fi
}
check_vols_out() {
  if [ ! -d /vols/out ]; then
    echo "No /vols/out for output!"
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
  rsync -ar --exclude-from=/tmp/rsync_exclude --delete-during /vols/src/ ./
}

function run_composer_install {
  composer install --no-interaction --no-scripts
}

function bundle {
  check_vols_out
  local version=${GITHUB_VERSION:-${CI_BRANCH:-v0.0.0}}
  DEST_DIR=/vols/out ./bin/release $version
}

sync
run_composer_install
bundle
