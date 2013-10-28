class base::apache2 {
  $web_packages = [
      "apache2",
      "libapache2-mod-php5"
  ]
  
  Package {
      ensure  => installed,
      require => Bulkpackage["web-packages"],
  }
  
  bulkpackage { "web-packages":
      packages => $web_packages,
      require  => [ Exec["apt-get_update"],
                    Exec["apt-get_upgrade"]
                  ],
  }
  
  package { $web_packages: }
  
  # apache2
  service { "apache2":
    ensure  => running,
    require => Package["apache2"],
  }
  exec { "apache2-reload":
    command     => "service apache2 reload",
    refreshonly => true,
    require => Package["apache2"],
  }
  exec { "apache2-mod-rewrite":
    command     => "a2enmod rewrite",
    notify => [Exec["apache2-reload"], ],
    require => Package["apache2"],
  }
  exec { "apache2-dissite-default":
    command     => "a2dissite default",
    notify => [Exec["apache2-reload"], ],
    require => Package["apache2"],
  }
  exec { "apache2-ensite":
    command     => "a2ensite ushahidi",
    notify => [Exec["apache2-reload"], ],
    require => [ Package["apache2"], File["/etc/apache2/sites-available/ushahidi"] ],
  }
  file { "/etc/apache2/sites-available/ushahidi":
    ensure  => "present",
    owner   => "root",
    group   => "root",
    mode    => 444,
    content => template("ushahidi.erb"),
    require => Package["apache2"],
  }

}
