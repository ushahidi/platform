#!/bin/bash
set -e

if [ -n "${PRIVATE_SSH_KEY}" ]; then
  /bin/echo -e "$PRIVATE_SSH_KEY" > $HOME/.ssh/id_rsa
  chmod 600 $HOME/.ssh/id_rsa
fi

# ==> Copy ansible scripts into container
git clone git@github.com:ushahidi/platform-cloud-ansible.git /opt --depth=5

if [ -n "${ANSIBLE_VAULT_PASSWORD}" ]; then
  /bin/echo -e "$ANSIBLE_VAULT_PASSWORD" > /opt/vpass
fi

# Append to ansible.cfg
cat >> /opt/ansible.cfg << EOM

[ssh_connection]
ssh_args=
EOM

# ==> Get latest deployment code from github
ansible-galaxy install -r roles.yml

# Perform variable substitution on parameters
#   i.e. if we get a parameter myvar="$CI_BRANCH" we substitute $CI_BRANCH for
#        its actual value in the environment, and get i.e. myvar="master"
args=()
for p in $@; do
    args+=(`printf '%s\n' $p | envsubst`)
done

# Execute parameter passed in arguments
echo executing: "${args[@]}"

exec "${args[@]}"

