class nginx {
  $web_packages = [
      "nginx-full",
      "php5-fpm"
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
  service { "nginx":
    ensure  => running,
    require => Package["nginx"],
  }
  exec { "apache2-reload":
    command     => "service nginx reload",
    refreshonly => true,
    require => Package["nginx"],
  }
}

