# ==> Import latest ubuntu/ansible build
FROM williamyeh/ansible:ubuntu14.04

# ==> Install git
RUN apt-get update && apt-get install git -y

# ==> Set workging directory to /opt
WORKDIR /opt

# ==> Copy ansible scripts into container
COPY ./ansible /opt

# ==> Get latest deployment code from github
RUN ["ansible-galaxy", "install", "-r", "roles.yml"]

# ==> Add wrapper script
COPY ./docker/deploy.run.sh /deploy.run.sh

# ==> Turn off host key checking for Ansible
ENV ANSIBLE_HOST_KEY_CHECKING False

# ==> Add deploy key to container
RUN mkdir -m 700 -p "$HOME/.ssh"

ENTRYPOINT [ "/bin/bash", "/deploy.run.sh" ]

