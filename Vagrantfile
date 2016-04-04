# Basic apache dev box complete with phpunit
# ready to go for ushahidi dev
Vagrant.configure("2") do |config|
  config.vm.box = "puppetlabs/ubuntu-14.04-64-puppet" # vagrantcloud
  config.vm.hostname = "ushahidi-platform.dev"
  config.vm.network "private_network", ip: "192.168.33.110"
  config.vm.synced_folder "./", "/var/www", id: "vagrant-root", :nfs => true
  config.vm.network "forwarded_port", guest: 22, host: 2210
  config.ssh.port = 2210

  config.vm.provider :virtualbox do |virtualbox|
    virtualbox.customize ["modifyvm", :id, "--name", "ushahidi-platform"]
    virtualbox.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    virtualbox.customize ["modifyvm", :id, "--memory", "512"]
    virtualbox.customize ["setextradata", :id, "--VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
  end

  config.vm.provision :shell do |shell|
    # upgrade all packages (including puppet) before using the puppet provisioner.
    # this excludes grub-pc since the hard drive id changes between VMs and will cause
    # an interactive prompt to appear and then error out, breaking the provisioning step.
    shell.inline = "DEBIAN_FRONTEND=noninteractive apt-mark hold grub-pc && apt-get update -y && apt-get upgrade -y -o Dpkg::Options::='--force-confdef' -o Dpkg::Options::='--force-confold'"
  end

  config.vm.provision :puppet do |puppet|
    puppet.environment_path = "puppet/"
    puppet.environment = "platform"
    puppet.options = ["--verbose"]
    puppet.facter = {
        # Optionally pass in a github oauth token through an environment variable
        "github_token" => ENV.fetch('github_token', '')
    }
  end
end
