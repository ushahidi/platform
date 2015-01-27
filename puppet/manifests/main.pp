Exec {
	path => "/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin",
}
File { mode => 0644 }

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
}

mysql::db { 'ushahidi':
	user     => 'ushahidi',
	password => 'lamulamulamu',
	host     => '%',
	grant    => ['ALL'],
	charset => 'utf8'
}

class { 'mysql::client': }

class { 'php': }

file { "/etc/php5/apache2/conf.d/99-ushahidi.ini":
	ensure  => "present",
	owner   => "root",
	group   => "root",
	mode    => 444,
	content => template("php-defaults.erb"),
	require => Package["php5"],
}

php::module {
	['curl',
	'gd',
	'imap',
	'json',
	'mcrypt',
	'mysqlnd'
	]:
}

class { 'composer':
	suhosin_enabled => false,
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
		allow          => 'from all',
		order          => 'allow,deny',
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

file { "/var/www/application/config/environments/development/database.php":
	ensure  => "present",
	content => template("database.erb"),
	require => File["/var/www/application/config/environments/development"]
}

file { "/var/www/httpdocs/.htaccess":
	ensure  => "present",
	content => template("htaccess.erb")
}

file { "/var/www/index.html":
	ensure  => "absent"
}

exec { "bin-update":
	path    => "/usr/local/node/node-default/bin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin",
	environment => [
			"HOME=/home/vagrant"
		],
	user    => "vagrant",
	command => "/var/www/bin/update",
	cwd     => "/var/www",
	logoutput => true,
	require =>  [ Mysql::Db["ushahidi"],
			File["/var/www/application/config/environments/development/database.php"],
			Package["php5-cli"],
			Package["php5-mysqlnd"],
			Class["composer"]
		]
}
