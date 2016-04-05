#!/bin/bash

if [ -n "${PRIVATE_SSH_KEY}" ]; then
  /bin/echo -e "$PRIVATE_SSH_KEY" > /root/.ssh/deploy
  chmod 600 /root/.ssh/deploy
fi

if [ -n "${ANSIBLE_VAULT_PASSWORD}" ]; then
  /bin/echo -e "$ANSIBLE_VAULT_PASSWORD" > /opt/vault.txt
fi

exec $*

