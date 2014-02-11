class base::php {
  $php_packages = [
      "php5-cli",
      "php5-curl",
      "php5-gd",
      "php5-imap",
      "php5-json",
      "php5-mcrypt",
      "php5-mysqlnd",
      "php5-xcache",
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

  file { "/etc/php5/apache2/conf.d/99-ushahidi.ini":
    ensure  => "present",
    owner   => "root",
    group   => "root",
    mode    => 444,
    content => template("php-defaults.erb"),
    require => Bulkpackage["php-packages"],
  }

}

