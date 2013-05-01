Exec {
    path => "/usr/sbin:/usr/bin:/sbin:/bin",
}

group { "puppet":
  ensure => "present",
}
File { owner => 0, group => 0, mode => 0644 }

file { '/etc/motd':
  content => "Welcome to your Vagrant-built virtual machine!
    Managed by Puppet.\n"
}

exec { "apt-get_update":
    command     => "/usr/bin/apt-get update",
    require     => [ File["norecommends"],
                     File["defaultrelease"],
                   ],
    tries       => 3
}

exec { "apt-get_upgrade":
    command     => "/usr/bin/apt-get -y upgrade",
    require     => [ Exec["apt-get_update"] ],
    tries       => 3,
    refreshonly => true
}

file { "norecommends":
    path    => "/etc/apt/apt.conf.d/02norecommends",
    content => "APT::Install-Recommends \"0\";",
}

file { "defaultrelease":
    path    => "/etc/apt/apt.conf.d/03defaultrelease",
    content => "APT::Default-Release \"precise\";",
}

$misc_packages = [
    "mysql-client",
    "curl",
    "wget",
    "git",
    "postfix",
    "byobu",
    "nfs-common",
]

Package {
    ensure  => installed,
    require => Bulkpackage["misc-packages"],
}

bulkpackage { "misc-packages":
    packages => $misc_packages,
    require  => [ Exec["apt-get_update"],
                  Exec["apt-get_upgrade"]
                ],
}

package { $misc_packages: }

include apache2
include php
include phpunit

#@todo: disable default site
#@todo: add ushahidi vhost and enable

