#!/bin/bash

# if someone invokes this with bash
set -e

# build release tarball
usage() {
    echo usage: $0 VERSION
    exit 2
}

test $# -eq 1 || usage
VERSION=$1
CWD=$(pwd)

TMP_DIR=$(mktemp -d 2>/dev/null || mktemp -d -t platform-build)
WORK_DIR=$TMP_DIR/ushahidi-platform-bundle-${VERSION}
mkdir $WORK_DIR

if [ -z "$DEST_DIR" ]; then
    DEST_DIR="${CWD}/build"
fi

echo "Copy to temp dir"
cp -R ./ $WORK_DIR

# Tar it up.
echo "Building tarball"
if [ ! -d "$DEST_DIR" ]; then
    mkdir -p "$DEST_DIR"
fi
TARFILE="${DEST_DIR}/ushahidi-platform-bundle-${VERSION}.tar"

tar -C $TMP_DIR -cf $TARFILE \
    --exclude 'build' \
    --exclude 'docs' \
    --exclude 'storage/logs/*' \
    --exclude '.travis.yml' \
    --exclude '.vagrant' \
    --exclude 'tmp' \
    --exclude '.env' \
    --exclude '.env.travis' \
    --exclude '.git' \
    --exclude '.gitbook' \
    ushahidi-platform-bundle-${VERSION}/

gzip -f $TARFILE
echo "Release tarball: ${TARFILE}.gz"

ls -la ${TARFILE}.gz

rm -rf $TMP_DIR
