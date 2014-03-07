class base::php {
  package {
    [
      "php5-cli",
      "php5-curl",
      "php5-gd",
      "php5-imap",
      "php5-json",
      "php5-mcrypt",
      "php5-mysqlnd",
      "php5-xcache",
      "php5-xdebug",
      "php-pear",
      "php5"
    ]:
    ensure => "present",
    require => [ Exec["apt-get_update"],
                 Exec["apt-get_upgrade"]
               ]
  }

  file { "/etc/php5/apache2/conf.d/99-ushahidi.ini":
    ensure  => "present",
    owner   => "root",
    group   => "root",
    mode    => 444,
    content => template("php-defaults.erb"),
    require => Package["php5"],
  }

}

