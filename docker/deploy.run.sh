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

exec $*

