class base::mysql {
  $db_packages = [
      "mysql-server-5.5",
  ]
  
  Package {
      ensure  => installed,
      require => Bulkpackage["db-packages"],
  }
  
  bulkpackage { "db-packages":
      packages => $db_packages,
      require  => [ Exec["apt-get_update"],
                    Exec["apt-get_upgrade"]
                  ],
  }
  
  package { $db_packages: }

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
    command => "/var/www/minion --task=migrations:run --up",
    require => Service["mysql"],
  }

}
