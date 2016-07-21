# ==> Import latest ubuntu/ansible build
FROM williamyeh/ansible:ubuntu14.04

# ==> Install git
RUN apt-get update && apt-get install git -y

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

