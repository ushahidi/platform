class base::mysql {
  package { "mysql-server-5.5":
    ensure => "installed",
    require  => [ Exec["apt-get_update"],
                  Exec["apt-get_upgrade"]
                ],
  }

  # mysql-5.5
  service { "mysql":
    ensure  => running,
    require => Package["mysql-server-5.5"],
  }

  define mysqldb( $user, $password ) {
    exec { "create-${name}-db":
      unless  => "mysql -u root ${name}",
      command => "mysql -u root -e \"CREATE DATABASE ${name};\"",
      require => Service["mysql"],
    }
    exec { "grant-${name}-db":
      unless  => "mysql -u ${user} -p${password} ${name}",
      command => "mysql -u root -e \"GRANT ALL ON ${name}.* to ${user}@localhost IDENTIFIED BY '${password}';\"",
      require => [ Service["mysql"],
                   Exec["create-${name}-db"]
                   ],
    }
  }

  mysqldb { "ushahidi":
    user     => "ushahidi",
    password => "lamulamulamu",
  }

  exec { "minion-migrations":
    path    => ["/var/www", "/usr/bin"],
    command => "minion --task=migrations:run --up",
    require =>  [ Service["mysql"],
                  File["/var/www/application/config/environments/development/database.php"],
                  Package["php5-cli"],
                  Package["php5-mysqlnd"]
                ]
  }

}
