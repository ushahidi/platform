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
  composer install
}

function bundle {
  check_vols_out
  local version=${GITHUB_VERSION:-${CI_BRANCH:-v0.0.0}}
  mkdir /tmp/ushahidi-platform-bundle-${version}; rsync -ar ./ /tmp/ushahidi-platform-bundle-${version}/
  tar -C /tmp -cz -f /vols/out/ushahidi-platform-bundle-${version}.tgz ushahidi-platform-bundle-${version}
}

sync
run_composer_install
bundle
