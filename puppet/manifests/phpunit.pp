class base::phpunit {
  # phpunit
  exec {"pear-channel-phpunit":
    command => "pear channel-discover pear.phpunit.de",
    require => Package["php-pear"],
    returns => [0,1]
  }
  exec {"pear-channel-ezno":
    command => "pear channel-discover components.ez.no",
    require => Package["php-pear"],
    returns => [0,1]
  }
  exec {"pear-channel-symfony-project":
    command => "pear channel-discover pear.symfony-project.com",
    require => Package["php-pear"],
    returns => [0,1]
  }
  exec {"pear-channel-symfony":
    command => "pear channel-discover pear.symfony.com",
    require => Package["php-pear"],
    returns => [0,1]
  }
  exec {"pear-update-channels": 
    command => "pear update-channels",
    require => [ Exec["pear-channel-phpunit"],
                Exec["pear-channel-ezno"],
                Exec["pear-channel-symfony"],
                Exec["pear-channel-symfony-project"],
                Package["php-pear"],
               ]
  }
  exec {"pear-upgrade": 
    command => "pear upgrade",
    require => [ Exec["pear-channel-phpunit"],
                Exec["pear-channel-ezno"],
                Exec["pear-channel-symfony"],
                Exec["pear-channel-symfony-project"],
                Exec["pear-update-channels"],
                Package["php-pear"],
               ]
  }
  exec {"pear-install-phpunit": 
    command => "pear install --soft --alldeps phpunit/PHPUnit",
    require => [ Exec["pear-channel-phpunit"],
                Exec["pear-channel-ezno"],
                Exec["pear-channel-symfony"],
                Exec["pear-channel-symfony-project"],
                Exec["pear-update-channels"],
                Exec["pear-upgrade"],
                Package["php-pear"],
               ],
    returns => [0,1]
  }
  exec {"pear-install-dbunit":
    command => "pear install --soft --alldeps phpunit/dbunit",
    require => [ Exec["pear-install-phpunit"],
                Package["php-pear"],
                ],
    returns => [0,1]
  }
}
