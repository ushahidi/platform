FROM ubuntu:trusty

ENV ANSIBLE_VERSION 2.1.2.0

RUN apt-get update && \
    apt-get install -y python-dev python-pip git libffi6 libffi-dev libssl1.0.0 libssl-dev unzip wget gettext && \
    pip install ansible==${ANSIBLE_VERSION} && \
    apt-get remove -y python-dev libffi-dev libssl-dev && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# ==> Set workging directory to /opt
WORKDIR /opt

# ==> Create .ssh dir
RUN mkdir -m 700 -p "$HOME/.ssh"

# ==> Add github host key to known hosts
RUN ssh-keyscan github.com >> $HOME/.ssh/known_hosts

# ==> Add wrapper script
COPY ./docker/deploy.run.sh /deploy.run.sh

# ==> Turn off host key checking for Ansible
ENV ANSIBLE_HOST_KEY_CHECKING False

ENTRYPOINT [ "/bin/bash", "/deploy.run.sh" ]

