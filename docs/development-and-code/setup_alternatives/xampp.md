---
description: >-
  The purpose of this guide is to set your local environment for development of
  the Ushahidi Platform with the help of the XAMPP bundle.
---

# Development environment with XAMPP

### Video-tutorials

The setup in this guide is demonstrated in below videos as well if you want to watch and follow the guide at the same time!

{% embed url="https://www.youtube.com/watch?v=zY80QpptKk0&feature=youtu.be" caption="Download the Platform code with GitHub Desktop, recorded in Windows" %}

{% embed url="https://www.youtube.com/watch?v=2byASqRp9hQ&feature=youtu.be" caption="Install XAMPP and Composer to setup the Ushahidi Platform Backend, recorded in Windows." %}

## Introduction

XAMPP is a bundle of server programs including three of the requirements for running the Ushahidi Platform API: Apache, MySQL and PHP.

XAMPP is available for Linux, Mac OS X and Windows. We are doing our best to ensure these instructions are useful for all three operating systems. However, there are a lot of details involved in getting everything right. Also, each of those operating systems can be found running in several different versions and configurations.

For this reason, you may find out that some instructions are missing or not working for you. If that's the case, please consider researching the solution \(you can [reach out for help](../../get-in-touch.md) too\), and contributing updates for this guide.

The two main components of the Ushahidi Platform are the API and the Client. We will cover each of them separately, starting with the API.

### Setting up the Platform API with XAMPP

#### Pre-requisites

* Install a Git client
  * A way to check if you have a Git client, is to open a terminal window and type the command `git` . If you receive an answer saying that the command is not found, you need to install.
  * There are several installation options suggested [here](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git).
* Make sure you have PHP 7.1.x, running with php-fpm  \(PHP 7.2.x is not supported at the time\). 
  * The following PHP Extensions are needed:
  * * curl
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
* Install XAMPP by downloading it from the [project website](https://www.apachefriends.org).

**Some useful tutorials and links to get the prerequisites set up:**

* Downgrading from php7.2 =&gt; php7.1 in Ubuntu \(added to this list 18/3-2019\): [https://gist.github.com/dosjota/9666a7274b4036588b92987b84267245](https://gist.github.com/dosjota/9666a7274b4036588b92987b84267245)
* Installing Composer on Ubuntu \(added to this list 18/3-2019\): [https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-18-04](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-18-04)

{% hint style="warning" %}
Ensure that you download the XAMPP package containing the **appropriate PHP version**. Check the [README](../../) for finding which one it is.
{% endhint %}

* _Mac:_
  * Open a terminal window and run the following command. **Close the terminal window after it's done.**

```bash
echo 'export PATH="/Applications/XAMPP/xamppfiles/bin:$PATH"' >> ~/.bash_profile
curl -sS https://getcomposer.org/installer | \
  sudo /Applications/XAMPP/xamppfiles/bin/php -- \
    --install-dir=/Applications/XAMPP/xamppfiles/bin --filename=composer
```

* _Linux:_
  * Open a terminal window and run the following command. **Log out and log back in after done.**

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
    * Search for the application XAMPP \(usually in the applications-window, but you can also search for it, a seach-window appears  by pressing \(usually\) cmd=&gt;space on Mac and  Super=&gt;A on Linux\).
    * Access the "Manage Servers" tab
    * Select "MySQL Database" and click the "Start" button on the right side
    * Select "Apache Web Server" and click the "Start" button on the right side
    * if the "Start" button is disabled, that's because the servers are already running
      * In Linux, there sometimes already is a Apache webserver running and this will prevent the XAMPP to run the server. In that case, open up a terminal-window and run `sudo service apache2 stop`
      * Try pressing Start button again for "Apace Web Server, it should now start.
* Go to [http://localhost](http://localhost) and verify that you see the XAMPP welcome page.
* Open a terminal window
  * Run `php -v` you should get a message with the installed PHP version, make sure this is the same version as you see in the XAMPP dashboard. If not, you need to switch php-version in the terminal. There are different ways of doing that depending on your setup, look for how to do it in your environment.
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
  * _Windows_: In the XAMPP control panel, click on "Shell" 
    * This usually opens a terminal with `C:\xampp` being the active folder
    * Run `cd htdocs`
  * _Linux_ and _Mac_: open your Terminal program, the active folder should be `home/dev`.
* Run `git clone https://github.com/ushahidi/platform.git platform` . This will download the Ushahidi Platform API code repository inside a folder named `platform` .

{% hint style="success" %}
It's **very important** that you have a clear idea of the location of your platform folder in the filesystem. Let's quickly recapitulate:

* _Windows_: `C:\xampp\htdocs\platform`
  * This may be different only if you chose a different path for XAMPP during its installation process.
* _Linux_ and _Mac_: `platform` , inside your home folder
  * To know your destination folder full path, open a new Terminal window and type the command `pwd`.
{% endhint %}

#### Configuring the API

We will configure the API now. We will do this continuing working on the same terminal window that was opened in the previous steps.

* Run `cd platform` 
* Run the appropriate command depending on your operating system: 
  * Windows: run `copy .env.example .env`
  * Linux and Mac: run `cp .env.example .env`
* Open `.env` with your IDE or text editor. This file is located inside your platform folder. 

{% hint style="info" %}
Hint on Linux and Mac: You can run `sudo nano .env` to open up the nano text-editor in the terminal. When you are finished editing, press ctrl-x and then Y when asked if you want to save.
{% endhint %}

{% hint style="warning" %}
On Windows File Explorer, the default is to hide the extension of the files \(the characters after the dot\). For that reason, the `.env` file may appear in your File Explorer window as a file with an empty name and of type "ENV file"
{% endhint %}

* Modify the file in the following way:
  * Change the `CACHE_DRIVER` to be `array` instead of `memcache` \(it's feasible set it up with memcache at some point, but for simplicity we use `array`\)
  * Change the `DB_HOST` to `127.0.0.1`
  * Change the `DB_USER` to `root` 
  * Change the `DB_PASSWORD` to be empty, so literally: `DB_PASSWORD=`
  * Change the `DB_DATABASE` to `platform`
* Run `composer install`. Wait while composer installs all the dependencies
* Run `composer migrate` to run the database migrations.
  * This will create all the necessary tables and a default `admin` user with password `administrator`

{% hint style="info" %}
In the event your system is telling you that `composer` is not a known command, you can try the following setup in the command line:

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

Then, use`php composer.phar {command}` instead of `composer {command}` in the two steps above. Such as:

```bash
php composer.phar composer install
php composer.phar composer migrate
```
{% endhint %}

### Configuring the web server

At this point you have the API ready to run, but need to setup your system and the Apache web server rules, in order to make it properly accessible through its own server name \(we'll use the server name "api.ushahidi.test" in this example\)

#### Configure the hosts file

{% hint style="info" %}
Configure the hosts file to match your API virtual host name to 127.0.0.1
{% endhint %}

Add the API virtual host name to your hosts file, by doing the following: appending a line with this content:`127.0.0.1 api.ushahidi.test`

* Windows:
  * Open the Notepad application **as administrator**.
    * Press the **Windows** key.
    * Type Notepad in the search field.
    * In the search results, right-click Notepad and select Run as administrator.
  * Open the file: `C:\Windows\System32\Drivers\etc\hosts`
    * When doing this from the Notepad "File" &gt; "Open" menu action,  you should make sure to change the default file filter from "Text Documents \(.txt\)" to "All Files"
  * Update the file, add a line at the bottom with these contents: `127.0.0.1 api.ushahidi.test`
* Linux / Mac :
  * Open the `/etc/hosts` file in an editor with administrator privileges \(i.e. with the terminal command `sudo nano /etc/hosts`\)
  * Update the file, appending a line with these contents: `127.0.0.1 api.ushahidi.test`

#### Configure the platform/httpdocs/.htaccess file

{% hint style="info" %}
Check the path, there are two locations where we will add different .htaccess files
{% endhint %}

In your platform folder, inside the folder `httpdocs`, edit the file `.htaccess`. Edit the contents of the file to match **exactly** these \(you can open the file to edit through `sudo nano .htaccess`\):

* File path: **platform/httpdocs/.htaccess**

{% code title="platform/httpdocs/.htaccess" %}
```text
# Turn on URL rewriting
RewriteEngine On

# Set base directory
RewriteBase /httpdocs
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

# Protect hidden files from being viewed
<Files .*>
  Order Deny,Allow
  Deny From All
</Files>

# Allow any files or directories that exist to be displayed directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Rewrite all other URLs to index.php/URL
RewriteRule .* index.php/$0 [PT]
```
{% endcode %}

#### Configure the platform/.htaccess file

{% hint style="info" %}
Check the path, there are two locations where we will add different .htaccess files
{% endhint %}

In your platform folder, edit the file `.htaccess`\(as before, you can open the file to edit it through `sudo nano .htaccess`\). The contents should match these, again, **exactly**:

* path: **platform/.htaccess**

{% code title="platform/.htaccess" %}
```text
# Turn on URL rewriting
RewriteEngine On


# Protect hidden files from being viewed under any circumstance
<Files .*>
  Order Deny,Allow
  Deny From All
</Files>

# Rewrite all URLs to httpdocs
RewriteRule .* httpdocs/$0 [PT]
```
{% endcode %}

Last, but not least, we are going to configure the web server to find your platform folder and link it to the "api.ushahidi.test" server name.

We are going to need some extra concentration here, so read carefully.

* We are setting ourselves to edit the web server's configuration file. So first thing, we need to know how to open it in an editor.
  * _Windows_: In the XAMPP control panel, click the "Config" button next to Apache and select "Apache \(httpd.conf\)". This will open an editor with the file.
  * _Linux_: go to XAMPP and select the tab "Manage Servers". Click on Apache Web Server and click the "Configure"-button to the right. Click on "Open Conf File" to open the configuration-file. A question if you really want to manually edit the file may appear. Click Yes in that case.
  * _Mac: ..._
* Now that we have the configuration file ready to edit, we are going to add a few lines at the bottom of that file. Those lines look like this:

```text
<VirtualHost *:80>
  ServerAdmin webmaster@localhost
  DocumentRoot "<your platform folder here>"
  ServerName api.ushahidi.test
  <Directory "<your platform folder here>">
    AllowOverride all
    Require all granted
  </Directory>
</VirtualHost>
```

{% hint style="warning" %}
Please note that **you must adjust the provided lines**. Wherever it says `"<your platform folder here>"`, you should change that for the full path of your platform folder, i.e. `"C:\xampp\htdocs\platform"` in the case of Windows.

If you chose a server name different from api.ushahidi.test , you should modify the line starting with `ServerName ...` as needed.
{% endhint %}

* After saving our changes, we need to restart the web server.
  * _Windows_: in the XAMPP control panel, next to "Apache", click the "Stop" button and then "Start"
  * _Linux: ..._
  * _Mac: ..._

All set! You should be able to access [http://api.ushahidi.test](http://api.ushahidi.test) now and see the default API response. Something like this:

```text
{"now":"2019-02-04T10:52:25+00:00","version":"3","user":{"id":null,"email":null,"realname":null}}
```

This doesn't look like much, but it's a very good sign that things are on track. Congrats!

{% hint style="info" %}
The Platform API doesn't provide any sort of clickable user interface, that is exactly what the Platform client is for. The Platform client is a web application that loads in your browser and talks to the Platform API to store and retrieve data as needed.

Jump on to the next section to see about setting up the Platform client.
{% endhint %}

### If something seems wrong

In case you are not getting the API response as suggested above, it's probable that something is not quite right.

Do not despair! There's a couple things you can try to see what's wrong:

1. Run the `composer verify` command. This will run some quick checks and may give you useful insights.
2. Carry on with installation and set up the Platform client. Bundled with the Platform client code there's a little tool called [Installation Helper](../installation-helper.md) that can run more checks and possibly help you out.

### Setting up the Platform client

The platform client comes with its own web server for development purposes. This means that we don't need to add or alter anything to the Apache and MySQL setup that we have done for the API. Both components will be running separately from each other.

#### Pre-requisites

All that is needed for setting up and developing with the platform client is [node.js javascript runtime](https://nodejs.org). Please, download and install the appropriate package for your operating system.

{% hint style="info" %}
At the time of this writing, only versions 6.x and 8.x of node.js have been tested, but 10.x is the default version family offered for download. Packages for version 8.x can be found [here](https://nodejs.org/dist/latest-v8.x/).

For Windows, look for the file with a name ended in "-x86.msi"
{% endhint %}

{% hint style="info" %}
For Linux and Mac OS X , probably the easiest way to control which version of node.js you are installing, is by using the "nvm" tool. There are several tutorials available that walk you through its set up. A good one can be found [here](https://nodesource.com/blog/installing-node-js-tutorial-using-nvm-on-mac-os-x-and-ubuntu/).
{% endhint %}

#### Installation

We have a separate document just for setting up the client. But, before you jump into that one, we've got to tell you one thing that you must remember.

At some point during the client set up, you will be asked to adjust the value of the variable `BACKEND_URL` . This is a variable that configures the client to send API requests to the right API backend.

As a result of the steps followed in this guide, the URL for the API backend is [http://api.ushahidi.test](http://api.ushahidi.test) . So remember that your backend configuration line would be: `BACKEND_URL=http://api.ushahidi.test`

With that clarified, please find below the link to the client set up guide.

{% page-ref page="setting-up-the-platform-client.md" %}

