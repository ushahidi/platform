# Installing for production environments

## Server requirements:

* Physical or virtual private server
* At least 2 GB of RAM memory \(recommended 4 GB\)
* 2 x86 64-bit CPU cores

## Required software:

* A linux-based system. We recommend using Ubuntu 16.4.
* PHP: 7.1.x, running with php-fpm \(PHP 7.2.x is not supported at the time\)
  * Make sure the same version of PHP is used in the CLI and FPM
* PHP Extensions:
  * curl
  * json
  * mbstring
  * mcrypt
  * bcmath
  * mysql
  * imap
  * gd
  * xml
  * zip
* Composer for PHP package management \( [https://getcomposer.org](https://getcomposer.org) \)
* Nginx version 1.10.x **\(Note: you can technically use apache, but this instructions will provide specific steps for Nginx only\)**
* MySQL server 5.7.x
* Node.js v6.x
* Redis v3.2
* Cron daemon
* Local e-mail forwarding setup \(mail command should be functional\)
* System clock continuously synchronized with ntpd, chrony or equivalent

## Networking environment

To run the Ushahidi Platform successfully in production, please ensure you have two hostnames available:

* A hostname for accessing the web client from a browser. This is the name that is most publicly visible, and appears in the browser address bar. For example, we use: yourdeploymentname.ushahidi.io
* A hostname for accessing the backend application. For example: yourdeploymentname.api.ushahidi.io . 

{% hint style="warning" %}
Ensure you have valid SSL certificates for both host names.
{% endhint %}

## Installation

### Clone the Ushahidi platform repositories

In your server, you should clone the “platform” and “platform-client” repositories. For the purpose of this documentation, we are going to assume the repository clones will be done in

* platform repository → /var/www/platform
* platform-client repository → /var/www/platform-client

### Creating the Platform API database

Once your MySQL database servers are up and running, you should:

* Create a new database. 
  * For the rest of this guide, we will assume the database name is "platform-db"
* Create a new database user with a password, this user should have access to the platform-db database. 
  * For the rest of this guide, we will assume the database user is named "platform-user", with a password "yourpassword"

Example MySQL statements for the above

```sql
CREATE DATABASE `platform-db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON `platform-db`.* TO ‘platform-user’@’%’ IDENTIFIED BY ‘yourpassword’;
```

### API installation

{% hint style="info" %}
This steps need to be executed in the directory where the platform codebase was cloned \(ie /var/www/platform\) 
{% endhint %}

#### .ENV file configuration

Create a new file named .ENV

{% code-tabs %}
{% code-tabs-item title=".ENV" %}
```php
## Laravel
APP_ENV=production
APP_DEBUG=false

# generate this APP_KEY with `php artisan key:generate`
APP_KEY={32characterkey}

APP_TIMEZONE=UTC

## Db connection information
DB_CONNECTION=mysql
DB_HOST={your-db-host} # example: localhost, if accessing the db locally (relative to the API server)
DB_PORT={your-db-port} # example/default : 3306 
DB_DATABASE={your-database-name} # example: platform-db
DB_USERNAME={your-database-user} # example: platform-user
DB_PASSWORD={your-database-password} # example: yourpassword

## Cache
CACHE_DRIVER=memcached
# Queues 
# This section will be particularly important once we launch release 4.2.x+ and later 
# since we will start providing access to queues for CSV exports then)
QUEUE_DRIVER=redis #
REDIS_HOST=127.0.0.1 # IP or hostname where redis is running
REDIS_PORT=6379 # Redis port

# Enabling or disabling the maintenance mode page
MAINTENANCE_MODE=0
```
{% endcode-tabs-item %}
{% endcode-tabs %}

#### Install the platform API dependencies

```bash
composer install
```

#### Run the database migrations

This will create all the tables and seed data required to run the API

```bash
./bin/phinx migrate -c application/phinx.php
```

#### Verify the directory permissions and ownership are correct

Ensure that the folders logs, cache and media/uploads under platform/application are all owned by the user that the web server is running as  \(for example, www-data\).

{% hint style="info" %}
You can check the user nginx is running with by running

ps aux \| grep 'ngnix'
{% endhint %}

Run the following command to ensure permissions are correctly set \(assuming www-data for both the user and group\)

 chown -R www-data:www-data storage/logs

 chown -R www-data:www-data storage/app

 chown -R www-data:www-data storage/framework

#### Setting up cronjobs to run recurring tasks

Tasks like receiving reports from external datasources and sending e-mail messages depend on cronjobs. Open the crontab for the www-data user \(or your nginx user\) in edit mode

```bash
crontab -u www-data -e
```

Add the following lines to the crontab

{% code-tabs %}
{% code-tabs-item title="crontab" %}
```bash
MAILTO=admin@example.com
 #ensure a valid email for system notifications
*/5 * * * * cd /var/www/platform && php artisan datasource:outgoing
*/5 * * * * cd /var/www/platform && php artisan datasource:incoming
*/5 * * * * cd /var/www/platform && php artisan savedsearch:sync
*/5 * * * * cd /var/www/platform && php artisan notification:queue

*/5 * * * * cd /var/www/platform && php artisan webhook:send
```
{% endcode-tabs-item %}
{% endcode-tabs %}

At this point, the backend is almost ready, but we still need to configure the web server and set up the client before we can see the application running.

#### Setting up the queue

From Ushahidi 4.2.x we use Laravel's queues to run CSV exports and other background tasks. You can run the queue worker manually to test:

```bash
php artisan queue:work
```

For production however you should run the queue using supervisor. First install supervisor

```bash
sudo apt-get install supervisor
```

Create a supervisor config file in `/etc/supervisor/conf.d/laravel-worker.conf`

```
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/forge/app.com/artisan queue:work --sleep=3 --tries=3 --timeout=290
autostart=true
autorestart=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/www/platform/storage/logs/worker.log
```

Start supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

For further information on Laravel queue's see the [laravel docs](https://laravel.com/docs/5.7/queues)

### Preparing the client to be served

Follow the instructions in the Platform Client installation steps for your /var/www/platform-client directory to setup the client. Make sure that you follow the production environment steps at the end \(\`gulp build\` instead of \`gulp\`\) .

{% page-ref page="setting-up-the-platform-client.md" %}

After you finished the set up, you should have a /var/www/platform-client/server/www directory with the generated files ready to be served by nginx. 

### Serving the API and client \(Nginx and PHP FPM setup\)

Create the /etc/nginx/sites-available/platform.conf file, referencing the httpdocs directory in the platform-api. Example settings below:

{% code-tabs %}
{% code-tabs-item title="/etc/nginx/sites-available/platform.conf" %}
```text
server {
    
listen 80 ;
    listen [::]:80 ;
    server_name your-site.api.example.com;
    charset UTF-8;
    root /var/www/platform/httpdocs;
    index index.php;
    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    
location / {
    
    try_files $uri $uri/ /index.php$uri?$args;
    }
    
# NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini
    location ^~ /index.php {
        
fastcgi_split_path_info ^(.+\.php)(/.+)$;
        
fastcgi_pass unix:/var/run/php7.0-fpm.sock;
        
fastcgi_index index.php;
        client_max_body_size 10m;
        fastcgi_read_timeout 600;
        include fastcgi_params;
        
break;
    }

}
```
{% endcode-tabs-item %}
{% endcode-tabs %}

Create the /etc/nginx/sites-available/platform-client.conf file, referencing the server/www directory in the platform-client.

{% code-tabs %}
{% code-tabs-item title="/etc/nginx/sites-available/platform-client.conf" %}
```text
server {
    listen 80 default_server;
    
listen [::]:80 ;
    server_name your-site.example.com;
    charset UTF-8;
    root /var/www/platform-client/server/www;
    
index index.html;
    location / {
        
try_files $uri $uri/ @missing;
    }
    
location /config.json {
        
if ($request_method = 'OPTIONS') {
            add_header 'Access-Control-Allow-Origin' '*';
            add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
            add_header 'Access-Control-Allow-Headers'
'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Co
ntrol,Content-Type,Content-Range,Range';
            add_header 'Access-Control-Max-Age' 1728000;
            add_header 'Content-Type' 'text/plain charset=UTF-8';
            
add_header 'Content-Length' 0;
return 204;
        }
        
if ($request_method = 'GET') {
            
add_header 'Access-Control-Allow-Origin' '*';
            add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
            
add_header 'Access-Control-Allow-Headers'
'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Co
ntrol,Content-Type,Content-Range,Range';
            
add_header 'Access-Control-Expose-Headers'
'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Co
ntrol,Content-Type,Content-Range,Range';
        
}
    
}
    
location @missing {
        rewrite ^ /index.html last;
    }

}
```
{% endcode-tabs-item %}
{% endcode-tabs %}



Run the following commands 

```bash
rm /etc/nginx/sites-enabled/default
;
ln -s /etc/nginx/sites-available/platform.conf /etc/nginx/sites-enabled/platform.conf
;
ln -s /etc/nginx/sites-available/platform-client.conf /etc/nginx/sites-enabled/platform-client.conf;

systemctl restart nginx.service
;
systemctl restart php7.0-fpm.service;
```

Configure PHP-FPM

Example contents for the file /etc/php/7.1/fpm/pool.d/www.conf

{% code-tabs %}
{% code-tabs-item title="/etc/php/7.1/fpm/pool.d/www.conf" %}
```text
[www]

user = www-data

group = www-data

listen = /run/php/php7.0-fpm.sock

listen.owner = www-data

listen.group = www-data
pm = dynamic
pm.max_children = 8
pm.start_servers = 4
pm.min_spare_servers = 1

pm.max_spare_servers = 4

pm.process_idle_timeout = 30s
```
{% endcode-tabs-item %}
{% endcode-tabs %}

### Verifying the API is running

  
Ensuring that the API backend is configured and operational can be achieved by accessing the base URL of the API. Example, if your API is hosted in https://_your-site.api.example.com_/, accessing that URL should output JSON like this:

```text
{"now":"2018-11-07T14:37:32+00:00","version":"3","user":{"id":null,"email":null,"realname":null}}
```

You should also check the  /api/v3/config resource , like this : https://_your-site.api.example.com_/api/v3/config and ensure it outputs a JSON document.

### Verifying the client is running and connected

{% hint style="info" %}
The client will only work if the API is operational.
{% endhint %}

Once you have verified the API, you should verify the client by accessing the URL where you hosted the client \(i.e. https://your-site.example.com \).

You should also logging in as an administrator to verify that the authentication system works. This can be achieved by using the username "admin" with the password "administrator" in v4, or the password "admin" in V3.

As an extra safety check, try creating a post in the platform by clicking the yellow + plus in the /views/data path or the /views/map path. 

{% hint style="warning" %}
Any updates the the platform client code or configuration will require a rebuild of the client. To do so, you can run "gulp build" like you did when installing the client in the server.
{% endhint %}

## Deploying Ushahidi for multiple languages

In order to display the web client in languages other than English, it’s necessary to download translations from Transifex. Authorized credentials are required to perform that step.

1. Create a user at [https://transifex.com](https://transifex.com) if you don't have one already.
2. Request access to the following project: [https://www.transifex.com/ushahidi/ushahidi-v3](https://www.transifex.com/ushahidi/ushahidi-v3)

Ushahidi will grant access to the transifex project once the request is received.

After Ushahidi grants access, modify the .ENV file in the platform-client to require the languages you need. For instance this is the .ENV file's LANGUAGE key when using spanish and english

```text
APP_LANGUAGES=en,es
```

After modifying the .ENV file, make sure to rebuild the client so the changes are reflected in the application. 

{% hint style="warning" %}
Any updates the the platform client code or configuration will require a rebuild of the client. To do so, you can run "gulp build" like you did when installing the client in the server.
{% endhint %}



