# -*- mode: ruby -*-
# vi: set ft=ruby :

# Basic apache dev box complete with phpunit
# ready to go for ushahidi dev
# You'll still need to handle some apache config

Vagrant::Config.run do |config|
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"
  config.vm.customize ["modifyvm", :id, "--memory", "512"]
  config.vm.network :hostonly, "192.168.33.110"
  config.vm.share_folder "www", "/var/www", ".", :nfs => true
  config.ssh.port = 2210
  config.vm.forward_port 22, 2210

  # Puppet provisioning
  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "puppet/manifests"
    puppet.manifest_file  = "base.pp"
    puppet.module_path = "puppet/modules"
    puppet.options = ["--templatedir", "/vagrant/puppet/templates"]
  end
end
