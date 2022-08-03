# -*- mode: ruby -*-
# vi: set ft=ruby :

require 'json'
require 'yaml'

VAGRANTFILE_API_VERSION ||= "2"
confDir = $confDir ||= File.expand_path("vendor/laravel/homestead", File.dirname(__FILE__))

homesteadYamlPath = File.expand_path("Homestead.yaml", File.dirname(__FILE__))
homesteadJsonPath = File.expand_path("Homestead.json", File.dirname(__FILE__))
afterScriptPath = "after.sh"
aliasesPath = "aliases"

require File.expand_path(confDir + '/scripts/homestead.rb')

Vagrant.require_version '>= 1.9.0'

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    if File.exist? aliasesPath then
        config.vm.provision "file", source: aliasesPath, destination: "/tmp/bash_aliases"
        config.vm.provision "shell" do |s|
            s.inline = "awk '{ sub(\"\r$\", \"\"); print }' /tmp/bash_aliases > /home/vagrant/.bash_aliases"
        end
    end

    if File.exist? homesteadYamlPath then
        settings = YAML::load(File.read(homesteadYamlPath))
    elsif File.exist? homesteadJsonPath then
        settings = JSON.parse(File.read(homesteadJsonPath))
    else
        abort "Homestead settings file not found in #{confDir}"
    end

    # Patch around the pitfalls of the imho dreadful composer update script in Homestead
    config.vm.provision "shell", inline: "mkdir -p /home/vagrant/.composer"

    # Homestead before v11.4 has a problem with trying to initialise Postgres databases
    # even if the postgres server is not available. Here we short-circuit that.
    $hide_postgres = <<-'SCRIPT'
    createdb_bin=/usr/bin/createdb
    if [ -e "$createdb_bin" ]; then
      mv $createdb_bin ${createdb_bin}.disabled
      ln -s /bin/true /usr/local/bin/createdb
    fi
    SCRIPT
    config.vm.provision "shell", inline: $hide_postgres

    Homestead.configure(config, settings)

    if File.exist? afterScriptPath then
        config.vm.provision "shell", path: afterScriptPath, privileged: false
    end

    if defined? VagrantPlugins::HostsUpdater
        config.hostsupdater.aliases = settings['sites'].map { |site| site['map'] }
    end
end
