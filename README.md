Ushahidi 3
============

[![Build Status](https://travis-ci.org/ushahidi/Lamu.png)](https://travis-ci.org/ushahidi/Lamu)

System Requirements
-------------------
To install the platform on your computer/server, the target system must meet the following requirements:

* PHP version 5.3.0 or greater
* Database Server
    - MySQL version 5.5 or greater
    - PostgreSQL support is coming
* An HTTP Server. Ushahidi is known to work with the following web servers:
    - Apache 2.2+
    - nginx
* Unicode support in the operating system

Installing
----------
1. Create a database
2. Copy ```appliication/config/database.php``` to ```application/config/environments/development/database.php```
3. Edit ```application/config/environments/development/database.php``` and set database, username and password params

	```
	return array
	(
		'default' => array
		(
			'type'       => 'mysql',
			'connection' => array(
				'hostname'   => 'localhost',
				'database'   => 'lamu',
				'username'   => 'lamu',
				'password'   => 'lamu',
				'persistent' => FALSE,
			),
			'table_prefix' => '',
			'charset'      => 'utf8',
			'caching'      => TRUE,
			'profiling'    => TRUE,
		)
	);
	```

4. Install the database schema using migrations

  ```./minion --task=migrations:run --up --group=3-0```
5. Copy ```appliication/config/init.php``` to ```application/config/environments/development/init.php```
6. Edit ```application/config/environments/development/init.php``` and change base_url to point the the httpdocs directory in your deployment
7. Copy ```httpdocs/template.htaccess``` to ```httpdocs/.htaccess```
8. Edit ```httpdocs/.htaccess``` and change the RewriteBase value to match your deployment url

Config
------

Base config files are in ```application/config/```.

You can add per-environment config overrides in ```application/config/environments/```. The environment is switched based on the ```KOHANA_ENV``` environment variable. 

Routes are configured in ```application/routes/default.php```. Additional routes can be added in per-environment routing files ie. ```application/routes/development.php```.

Extras
------

### Vagrantfile

We've included a Vagrantfile and puppet manifests to help build a quick development box. Install [Vagrant](http://www.vagrantup.com/), then run ```vagrant up``` to get started!

### Travis-CI

Unit and functional tests are run automatically by [Travis-CI](https://travis-ci.org/ushahidi/Lamu).
See [.travis.yml](https://github.com/ushahidi/Lamu/blob/master/.travis.yml) for config details.

### Testing

We use PHPUnit for unit tests, and Behat and Mink for functional testing.
You can install the Behat, Mink, PHPUnit and other required packages using [Composer](getcomposer.org). Just run
```composer install```

Behat and PHPUnit will be installed to ```./bin``` at the root of the repository.

Create a behat config file by copying ```application/tests/behat.template``` to ```application/tests/behat.yml```. Edit the file to set your deployments base url.
Run the tests as follows:
```
./bin/behat --config application/tests/behat.yml
./bin/phpunit -c application/tests/phpunit.xml
```
