Ushahidi 3
============

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
2. Copy ```application/config/database.template``` to ```application/config/database.php```
3. Edit ```application/config/database.php``` and set database, username and password params

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

