class base::php {
  $php_packages = [
      "php5-mysqlnd",
      "php5-mcrypt",
      "php5-curl",
      "php5-imap",
      "php5-xcache",
      "php5-gd",
      "php5-xdebug",
      "php5-cli",
      "php-pear",
      "php5"
  ]
  
  Package {
      ensure  => installed,
      require => Bulkpackage["php-packages"],
  }
  
  bulkpackage { "php-packages":
      packages => $php_packages,
      require  => [ Exec["apt-get_update"],
                    Exec["apt-get_upgrade"]
                  ],
  }
  
  package { $php_packages: }
}

