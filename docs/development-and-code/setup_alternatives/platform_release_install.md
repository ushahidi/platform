# \[API  & Client\] Bundled release install

## Installation Overview

We recommend most users install the latest release.

If you're a developer and you want to extend Ushahidi or contribute to our code, follow the development install instructions in [the README](https://github.com/ushahidi/platform/blob/develop/README.md).

## Installing the latest release

The release bundles are pre-built compressed files for you, which don't require further building or downloading. These files bundles are available from the platform-release repository in Github. The files are named ushahidi-platorm-release-vX.Y.Z.tar.gz .

If you are in a shared hosting solution and not sure if it supports Ushahidi, you should check the requirements with your provider.

The installation procedure will vary depending on your setup, but the requirements in all cases are

* PHP &gt;=7.0 &lt;=7.1.
* A web server that supports PHP. This can be apache 2 or nginx.
* PHP invokable from command line
* The following PHP modules installed:
  * curl
  * json
  * mcrypt
  * mysqli
  * pdo
  * pdo\_mysql
  * imap
  * gd
* A MySQL database server: these instructions assume that you know how to create a database in your MySQL server and obtain user credentials with access to such database.

The instructions and example commands are written specifically for Debian Linux or a derivative of it \(Ubuntu, Mint, etc\). You may have to adjust some things if you are installing on a different flavour of Linux, or a different OS.

### Apache 2 with mod\_php

* Ensure mod\_rewrite is installed and enabled in your apache server.
* Copy into your document root the contents of the `html/` folder after unzipping the ushahidi-platform-release-v4.X.Y.tar.gz bundle file.
* The `dist/` folder contains the suggested configurations for the virtual host \(apache-vhost.conf\). The configs are quite default, you just need to ensure that there is an "AllowOverride" directive set to "All" for your document root \(where the app has been unzipped\).
* Create a `platform/.env` file. This file will contain your database credentials and other important configurations. Use the following contents as a guide, lines starting with the character `#` are comments and you don't need to copy them

  ```bash
  DB_HOST=<address of your MySQL server>
  DB_DATABASE=<name of the database in your server>
  DB_USERNAME=<user to connect to the database>
  DB_PASSWORD=<password to connect to the database>
  DB_TYPE=MySQLi

  APP_ENV=local
  APP_DEBUG=false
  # It is encouraged you create your own APP_KEY , it MUST be 32 characters long! 
  APP_KEY=SomeRandomKey!!!SomeRandomKey!!!
  # This is only relevant for debug level stuff like timestamps in log messages
  APP_TIMEZONE=UTC

  CACHE_DRIVER=array
  # See comments down in the doc for other options
  QUEUE_DRIVER=sync
  ```

* Run the database migrations, execute this command from the `platform` folder:
  * `php artisan migrate`
* Ensure that the folders logs, cache and media/uploads under platform/application are all owned by the user that the web server is running as.
  * i.e. in Debian derived Linux distributions, this user is www-data, belonging to group www-data, so you would run: `chown -R www-data:www-data platform/storage/{logs,app,framework}`
* Generate the secrets key required to secure the user authentication subsystem \(passport\):
  * `php artisan passport:keys`
* Set up the cron jobs for tasks like receiving reports and sending e-mail messages.

You'll need to know again which user your web server is running as. We'll assume the Debian standard www-data here. Run the command crontab -u www-data -e and ensure the following lines are present in the crontab:

```text
MAILTO=<your email address for system alerts>
*/5 * * * * cd <your document root>/platform && php ./artisan datasource:outgoing >> /dev/null
*/5 * * * * cd <your document root>/platform && php ./artisan datasource:incoming >> /dev/null
*/5 * * * * cd <your document root>/platform && php ./artisan savedsearch:sync >> /dev/null
*/5 * * * * cd <your document root>/platform && php ./artisan notification:queue >> /dev/null
*/5 * * * * cd <your document root>/platform && php ./artisan webhook:send >> /dev/null
```

* Restart your apache web server and access your virtual host. You should see your website and be able to login with the credentials:
  * user name: admin
  * password: admin

{% hint style="warning" %}
Make sure to try to log in at least once. The system will ask you right away to provide a valid e-mail address \(that will override the "admin" user name\).

That's also a great chance to set a more secure password. 🔐👍
{% endhint %}

## nginx with php-fpm

The procedure is pretty similar to the one detailed for apache above, with the following exceptions.

* Step 1: mod\_rewrite is specific for Apache, in nginx the module is named ngx\_http\_rewrite\_module. It's usually included and enabled.
* Step 3: instead of configuring Apache, you would need to configure nginx. For configuring nginx see the example nginx-site.conf in the dist folder. You would usually drop this file in a place where it's included from the main configuration file. It assumes php-fpm is listening in port 9000 of localhost.

  The default php-fpm configuration should work. Most importantly, you need to ensure the listen directive matches the fastcgi\_pass directive in the nginx host configuration file.

  Once you are done, restart both your nginx and php-fpm services.

## Shared hosting \(Cpanel, Dreamhost, Bluehost, etc\)

In general, the instructions for Apache can be taken as a guideline.

Each shared hosting provider comes with their own set of particularities, so we can only provide general directions here.

In all cases, you'll need to ensure that:

* Decompress the release file and place the contents of the `html` folder in the webroot of your shared hosting domain or subdomain.
* Create a database for your website and write the access details in the `platform/.env` file \(as per step 4 of Apache 2 instructions\).
* Also, you must have command line access \(SSH\) in order to run the `php artisan migrate` and other `artisan` commands as outlined above.
* Most importantly, a URL rewriting mechanism has to be in place so that requests to /platform/api/v3/ _\_are to be forwarded to the `index.php` script inside `platform/httpdocs`_. _When invoking that script, the "api/v3/\*" part of the url should be passed to the script into the a `$_SERVER` or environment variable. If your host uses Apache and supports_ .htaccess\_ files, most of this should be taken care of for you.

## Something seems wrong?

If something doesn't seem to work we suggest giving a try to open your deployment website address, but adding a "/verifier" at the end of it.

{% hint style="info" %}
For instance, if the address of your deployment is [https://ushahidi.example.com](https://ushahidi.example.com) , we suggest you to try to open: [https://ushahidi.example.com\*\*/verifier\*\*](https://ushahidi.example.com**/verifier**)
{% endhint %}

The latest releases of the Ushahidi Platform come with a little handy tool called "[Installation Helper](../installation-helper.md)" which is started by accessing that specific address in the deployment.

If something is wrong, this tool may provide you with useful information about what exactly seems to be the cause.

## Queue drivers \(and "sync" driver issues\)

The Ushahidi Platform API uses a queue system for running some end-user requested operations in the background. At the moment of this writing, such operations are CSV importing and exporting. More may come up in the future.

The challenge during installation is that queue systems usually take additional set up. By using the "sync" queue driver as a first option, we are removing the need of that additional set up. This is not magic, just a compromise, because there will be effectively no queue: the jobs will run **synchronously, right away, when the request is made by the user**.

The problem with a synchronous set up is that long running operations \(imagine, creating a CSV with thousands of rows\) can take a long time and the web server may throw a timeout before the operation is complete.

If you are running into these problems, you'll need the additional setup detailed in this section.

### Redis

If installing Redis is possible for you, we would recommend that, as it would also allow you to enable caching through it.

In this case, get Redis installed \(don't forget the `php-redis` extension in your web server host\) and modify the following queues in the `platform/.env` file:

```bash
CACHE_DRIVER=redis
QUEUE_DRIVER=redis
REDIS_HOST=localhost  # or other host, if running separately
REDIS_PORT=6379       # or other port, if your installation required that
```

### Can't do Redis ...

If Redis is not available for you, you could give a shot to the `database` driver, which will use the database to keep track of the jobs. This may be less ideal in high traffic installations, but should work well otherwise.

In that case, change this entry in the `platform/.env` file :

```text
QUEUE_DRIVER=database
```

and run the following commands \(from your `platform` folder\):

```text
php artisan queue:table
php artisan migrate
```

### Queue workers

Great! So jobs will be sent over to Redis or the database, instead of holding up the web server responses. But who does the work these jobs entail? You need queue workers.

And, as with many a labor collective, the main challenge is to keep them working when you are not looking. We document two possibilities here:

#### You can install / configure a system process supervisor

Examples of process supervisors are systemd, upstart, pm2 , supervisord or chaperone.

Here we'll document how to configure [supervisord](http://supervisord.org/) , which is one of the popular options. You would need to add the following block to your supervisord configurations \(usually found under `/etc/supervisor` and `/etc/supervisor/conf.d`

```text
[program:ushahidi-platform-workers]
process_name=%(program_name)s_%(process_num)02d
command=php <your document root>/platform/artisan queue:listen --sleep=3 --tries=3 --timeout=290
autostart=true
autorestart=true
user=www-data    # !! this only works for debian/ubuntu systems
numprocs=2       # you can increase this for more workers
redirect_stderr=true
stdout_logfile=<your document root>/platform/storage/logs/worker.log
```

Then the following command makes sure workers are started if they haven't already:

```text
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ushahidi-platform-workers:*
```

If you check your running processes \(i.e. with the `ps -ef` \) you should see some processes with names similar to `artisan queue:work`

#### You can't install or configure anything system related, but you've got cron

If you can't set up a process supervisor, you may still have old trusty cron available.

The idea here is that you can set up a cron job, to wake up your queue workers every few minutes, get their job done and go back to sleep until the next interval comes.

This can be done by adding the following line to your crontab \(interval of 10 minutes\):

```text
*/10 * * * * cd <your document root>/platform && php ./artisan queue:work --stop-when-empty
```

You may adjust the interval to be shorter. In extreme cases with lots of jobs, your worker processes may pile up if the interval is too short.

{% hint style="info" %}
For more material on this topic, see [this document](https://laravel.com/docs/5.8/queues) from the Laravel project documentation website.
{% endhint %}

