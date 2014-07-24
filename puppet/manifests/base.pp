Exec {
    path => "/usr/sbin:/usr/bin:/sbin:/bin",
}

import "apache2.pp"
import "mysql.pp"
import "php.pp"

group { "puppet":
  ensure => "present",
}
File { mode => 0644 }

file { '/etc/motd':
  content => "Welcome to your Vagrant-built virtual machine!
    Managed by Puppet.\n"
}

file { '/var/www/application/cache':
  ensure => directory,
  mode   => '0777',
}

file { '/var/www/application/logs':
  ensure => directory,
  mode   => '0777',
}

file { '/var/www/application/media/uploads':
  ensure => directory,
  mode   => '0777',
}

file { "/var/www/application/config/environments":
  ensure => directory
}

file { "/var/www/application/config/environments/development":
  ensure => directory,
  require => File["/var/www/application/config/environments"]
}

file { "/var/www/application/config/environments/development/database.php":
  ensure  => "present",
  content => template("database.erb"),
  require => File["/var/www/application/config/environments/development"]
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
    content => "APT::Default-Release \"saucy\";",
}

package {
  [
    "mysql-client",
    "curl",
    "wget",
    "git",
    "postfix",
    "byobu",
    "nfs-common",
  ]:
  ensure  => installed,
  require  => [
    Exec["apt-get_update"],
    Exec["apt-get_upgrade"]
  ]
}

include base::apache2
include base::mysql
include base::php
