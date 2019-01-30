---
description: >-
  The purpose of this guide is to set your local environment for development of
  the Ushahidi Platform with the help of the XAMPP bundle.
---

# Development environment with XAMPP

## Introduction

XAMPP is a bundle of server programs including three of the requirements for running the Ushahidi Platform API: Apache, MySQL and PHP.

XAMPP is available for Linux, Mac OS X and Windows. We are doing our best to ensure these instructions are useful for all three operating systems. However, there are a lot of details involved in getting everything right. Also, each of those operating systems can be found running in several different versions and configurations.

For this reason, you may find out that some instructions are missing or not working for you. If that's the case, please consider researching the solution \(you can [reach out for help](../../contributing-or-getting-involved/get-in-touch.md) too\), and contributing updates for this guide.

The two main components of the Ushahidi Platform are the API and the Client. We will cover each of them separately, starting with the API.

### Setting up the Platform API with XAMPP

#### Pre-requisites

* Install a Git client
  * A way to check if you have a Git client, is to open a terminal window and type the command `git` . If you receive an answer saying that the command is not found, you need to install.
  * There are several installation options suggested [here](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git).
* Install XAMPP by downloading it from the [project website](https://www.apachefriends.org).

{% hint style="warning" %}
Ensure that you download the XAMPP package containing the **appropriate PHP version**.  Check the [README](../../) for finding which one it is.
{% endhint %}

* _Mac:_
  * Open a terminal window and run the following command. Close the terminal window after it's done.

```bash
echo 'export PATH="/Applications/XAMPP/xamppfiles/bin:$PATH"' >> ~/.bash_profile
curl -sS https://getcomposer.org/installer | \
  sudo /Applications/XAMPP/xamppfiles/bin/php -- \
    --install-dir=/Applications/XAMPP/xamppfiles/bin --filename=composer
```

* _Linux:_
  * Open a terminal window and run the following command. Log out and log back in after done.

```bash
echo 'export PATH="/opt/lampp/bin:$PATH"' >> ~/.bash_profile
curl -sS https://getcomposer.org/installer | \
  sudo /opt/lampp/bin/php -- \
    --install-dir=/opt/lampp/bin --filename=composer
```

* _Windows:_
  * Install Composer, the package manager for PHP. You may do that by following the instructions for your environment [in the project website](https://getcomposer.org/) .
  * If your XAMPP Control Panel was open, you may need to close it at this point.

#### Verify the installation

* Open the XAMPP Control Panel and make sure that both MySQL and Apache are running.
  * _Windows_: click the "Start" button next to MySQL and Apache modules
  * _Linux and Mac_:
    * Access the "Manage Servers" tab
    * Select "MySQL Database" and click the "Start" button on the right side
    * Select "Apache Web Server" and click the "Start" button on the right side
    * if the "Start" button is disabled, that's because the servers are already running
* Go to [http://localhost](http://localhost) and verify that you see the XAMPP welcome page.
* Open a terminal window
  * Run `php -v` you should get a message with the installed PHP version
  * Run `composer -V` you should get a message with the installed Composer version

#### Set up the database

* Create a database for saving the platform data
  * Open the [http://localhost/phpmyadmin](http://localhost/phpmyadmin) in your browser
  * Create a database named "platform"
    * On the left hand side of the phpmyadmin interface, you will see a list of databases. Click the "New" label on top of it
    * On the right hand side:
      * Provide the database name \("platform"\)
      * Right next to it you will find a drop down to select the character encoding and collation. Scroll near to the bottom of the list and select "utf8mb4\_unicode\_ci"
      * Click "Create"

#### Obtain the code

* We need to do some work from a terminal window, let's open one.
  * Windows: In the XAMPP control panel, click on "Shell" 
    * This usually opens a terminal with `C:\xampp` being the active folder
    * Run `cd htdocs`
  * Linux and Mac: open your Terminal program
* Run `git clone https://github.com/ushahidi/platform.git platform` . This will download the Ushahidi Platform API code repository inside a folder named `platform` .

#### Configuring the API

We will configure the API now. We will do this continuing working on the same terminal window that was opened in the previous steps.

* Run `cd platform` 
* Run the appropriate command depending on your operating system: 
  * Windows: run `copy .env.example .env`
  * Linux and Mac: run `cp .env.example .env`
* Open `.env` with your IDE or text editor. The location of this file will depend on your choice during XAMPP installation. Here are the defaults for each operating system:
  * _Windows:_ `C:\xampp\htdocs\platform`
  * _Mac:_ `/Applications/XAMPP/htdocs/platform`
  * Change the `CACHE_DRIVER` to be `file` instead of `memcache` \(it's feasible set it up with memcache at some point, but for simplicity we use `file`\)
  * Change the `DB_HOST` to `127.0.0.1`
  * Change the `DB_USER` to `root` 
  * Change the `DB_PASSWORD` to be empty, so literally: `DB_PASSWORD=`
  * Change the `DB_DATABASE` to `platform`
* Run `composer install`. Wait while composer installs all the dependencies
* Run `composer migrate` to run the database migrations.
  * This will create all the necessary tables and a default `admin` user with password `administrator`

{% hint style="info" %}
A note on Composer. If for some reason the command `composer` wasn't installed to be globally available in your system, you can try using`php composer.phar {command}` instead of `composer {command}` in the two steps above
{% endhint %}

#### Configuring the web server

* At this point you have the API ready to run, but need to setup some apache rules to be able to access it correctly
* Add the api url to your hosts file \(`127.0.0.1 api.ushahidi.test`\)
* Add this to file `platform/httpdocs/.htaccess`:

```text
#Turn on URL rewriting
RewriteEngine On

#Set base directory
RewriteBase /httpdocs
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

# Protect hidden files from being viewed
<Files .*>
  Order Deny,Allow
  Deny From All
</Files>

#Uncomment to force redirection to https site.
#RewriteCond %{HTTP:X-Forwarded-Proto} =http
#RewriteRule ^(.*)$ https://%{HTTP_HOST}%{ENV:REWRITEBASE}$1 [R=301,L]

#Allow any files or directories that exist to be displayed directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

#Rewrite all other URLs to index.php/URL
RewriteRule .* index.php/$0 [PT]
```

* Add this to file `platform/.htaccess`:

```text
#Turn on URL rewriting
RewriteEngine On

# Protect hidden files from being viewed under any circumstance
<Files .*>
  Order Deny,Allow
  Deny From All
</Files>

# Rewrite all URLs to httpdocs
RewriteRule .* httpdocs/$0 [PT]
```

* Windows: in your httpd.conf file \(open xampp =&gt; config -&gt; httpd.conf\) , add this virtualhost:

```
<VirtualHost *:80>
  ServerAdmin webmaster@localhost
  DocumentRoot "C:/xampp/htdocs/platform"
  ServerName api.ushahidi.test
  <Directory "C:/xampp/htdocs/platform">
    AllowOverride all
  </Directory>
</VirtualHost>
```

You're all done. You should be able to access [http://api.ushahidi.test](http://api.ushahidi.test) now and see the default API response.

