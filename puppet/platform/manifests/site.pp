Exec {
	path => "/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin",
}
File { mode => "0644" }

file { '/etc/motd':
	content => "Welcome to your Ushahidi Platform virtual machine!
	Managed by Puppet.\n"
}

class { 'apt': }

# Update before install any packages
exec { 'apt-update':
	command => '/usr/bin/apt-get update'
}
Apt::Source <| |> -> Exec['apt-update'] -> Package <| |>

package {
	[
	"curl",
	"wget",
	"postfix",
	"byobu",
	"nfs-common",
	"php5",
	"libapache2-mod-php5",
	"php5-cli",
	"php5-curl",
	"php5-gd",
	"php5-imap",
	"php5-json",
	"php5-mcrypt",
	"php5-mysqlnd"
	]:
	ensure  => installed,
	require  => [
		Exec["apt-update"],
		#Exec["apt-upgrade"]
	]
}

# MySQL setup
class { '::mysql::server':
	package_name     => 'mysql-server-5.5',
	override_options => {
		'mysqld' => {
			'max_connections' => '1024',
			'bind_address'    => '0.0.0.0'
		}
	},
	restart          => true
}

mysql::db { 'ushahidi':
	user     => 'ushahidi',
	password => 'lamulamulamu',
	host     => '%',
	grant    => ['ALL'],
	charset => 'utf8'
}

class { 'mysql::client': }

file { "/etc/php5/apache2/conf.d/99-ushahidi.ini":
	ensure  => "present",
	owner   => "root",
	group   => "root",
	mode    => "444",
	content => template("platform/php-defaults.erb"),
	require => Package["libapache2-mod-php5"],
}

exec { "php-modules":
	command => "php5enmod mcrypt imap"
}

class { 'composer':
	suhosin_enabled => false,
	github_token => if ( $github_token and $github_token != '' ) { $github_token } else { undef }
}

# Apache setup

# Define directories first so that apache class doesn't try to set permissions
file { '/var/www':
	ensure => directory,
}

file { '/var/www/httpdocs':
	ensure => directory,
}

class { 'apache':
	default_vhost => false,
	require => File["/var/www"],
	mpm_module => 'prefork'
}

apache::mod { 'rewrite': }

class {'::apache::mod::php':
	#package_name => "php54-php",
	#path         => "${::apache::params::lib_path}/libphp54-php5.so",
}

apache::vhost { 'ushahidi.dev':
	port          => '80',
	docroot       => '/var/www/httpdocs',
	directories   => [{ path => '/var/www/httpdocs',
		allow_override => 'All',
		auth_require   => 'all granted',
		options        => ['Indexes', 'FollowSymLinks', 'MultiViews']
	}],
	require => File["/var/www/httpdocs"],
}

# Ushahidi directories and files
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

file { "/var/www/.env":
	ensure  => "present",
	content => template("platform/env.erb")
}

file { "/var/www/httpdocs/.htaccess":
	ensure  => "present",
	content => template("platform/htaccess.erb")
}

file { "/var/www/html/index.html":
	ensure  => "absent"
}

exec { "bin-update":
	path    => "/usr/local/node/node-default/bin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin",
	environment => [
			"HOME=/home/vagrant"
		],
	user    => "vagrant",
	command => "/var/www/bin/update --no-interaction",
	cwd     => "/var/www",
	logoutput => true,
	timeout => 0,
	require =>  [ Mysql::Db["ushahidi"],
			File["/var/www/.env"],
			Package["php5-cli"],
			Package["php5-mysqlnd"],
			Class["composer"]
		]
}
