# Basic apache dev box complete with phpunit
# ready to go for ushahidi dev
Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu-1310-x64-virtualbox-puppet"
  config.vm.box_url = "http://puppet-vagrant-boxes.puppetlabs.com/ubuntu-1310-x64-virtualbox-puppet.box"
  config.vm.network "private_network", ip: "192.168.33.110"
  config.vm.synced_folder "./", "/var/www", id: "vagrant-root", :nfs => true
  config.vm.network "forwarded_port", guest: 22, host: 2210
  config.ssh.port = 2210

  config.vm.provider :virtualbox do |virtualbox|
    virtualbox.customize ["modifyvm", :id, "--name", "lamu"]
    virtualbox.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    virtualbox.customize ["modifyvm", :id, "--memory", "512"]
    virtualbox.customize ["setextradata", :id, "--VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
  end

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "puppet/manifests"
    puppet.manifest_file  = "base.pp"
    puppet.module_path = "puppet/modules"
    puppet.options = ["--verbose", "--templatedir", "/vagrant/puppet/templates"]
  end
end
