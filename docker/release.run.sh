#/bin/sh

set -ex

if [ -z "$GITHUB_RELEASE_TOKEN" ]; then
  echo "Please provide a GITHUB_RELEASE_TOKEN environment variable!"
fi

export GITHUB_TOKEN=$GITHUB_RELEASE_TOKEN

GITHUB_ORG=${GITHUB_ORG:-ushahidi}
GITHUB_REPO_NAME=${GITHUB_REPO_NAME:-$CI_REPO_NAME}
GITHUB_VERSION=${GITHUB_VERSION:-$CI_BRANCH}

ghr() {
  local cmd=$1
  shift 1
  /go/bin/github-release $cmd \
    --user $GITHUB_ORG \
    --repo $GITHUB_REPO_NAME \
    $*
}

if ghr info --tag $GITHUB_VERSION ; then
  # release already exists
  ghr edit --tag $GITHUB_VERSION --name $GITHUB_VERSION --pre-release
else
  # release has to be created
  ghr release --tag $GITHUB_VERSION --name $GITHUB_VERSION --pre-release
fi

for f in $(find /release -type f); do
  ghr upload --tag $GITHUB_VERSION --name $(basename $f) --file $f
done
