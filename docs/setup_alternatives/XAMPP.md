# Development setup with XAMPP
- Install XAMPP
- Verify that Mysql and Apache are running in the xampp control panel
- Go to http://localhost and verify that you see the XAMPP dashboard page
- In the XAMPP control panel, click on the button "Admin" for MySQl
	- in the mysql dashboard, create a new database called `platform`. 
- In the XAMPP control panel, click on "Shell" 
- Run `cd htdocs`
- Run `git clone git@github.com:ushahidi/platform.git platform` to clone the platform code inside your htdocs directory
- `cd platform` 
- Install composer following the instructions for your environment [here](https://getcomposer.org/doc/00-intro.md)
- Copy `.env.example` to a new file called `.env` to create the default .env file
- open .env with your IDE or text editor. 
    - Change the CACHE_DRIVER to be file instead of memcache (you can also set it up with memcache, but for simplicity we 
    - Change the DATABASE HOST to 127.0.0.1
    - Change the DATABASE USER to your mysql user  (root in the default XAMPP install)
    - Change the DB PASSWORD  (empty by default with XAMPP)
	- Change the DB name to `platform`
- A note on composer: if you didn't setup composer globally, you should use `php composer.phar {command}` instead of `composer {command}` in the next two steps 
    - run `composer install`. Wait while composer installs all the dependencies
    - run `composer migrate` to run the database migrations. This will create all the necessary tables and a default `admin` user with password `administrator`
- at this point you have the API ready to run, but need to setup some apache rules to be able to access it correctly.
- Add the api url to your hosts file (127.0.0.1 api.ushahidi.test)
- Add this file platform/httpdocs/.htaccess:
```
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

# Uncomment to force redirection to https site.
#RewriteCond %{HTTP:X-Forwarded-Proto} =http
#RewriteRule ^(.*)$ https://%{HTTP_HOST}%{ENV:REWRITEBASE}$1 [R=301,L]

# Allow any files or directories that exist to be displayed directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Rewrite all other URLs to index.php/URL
RewriteRule .* index.php/$0 [PT]
```
- Add this file platform/.htaccess
```
# Turn on URL rewriting
RewriteEngine On

# Set base directory
#RewriteBase /platform

# Protect hidden files from being viewed
<Files .*>
Order Deny,Allow
Deny From All
</Files>

# Rewrite all other URLs to httpdocs
RewriteRule .* httpdocs/$0 [PT]

```
- In your httpd.conf file (open xampp => config -> httpd.conf) , add this virtualhost
```
<VirtualHost *:80>
ServerAdmin webmaster@localhost
DocumentRoot "C:/newxamp/htdocs/platform"
ServerName ushahidi.api.test
<Directory "C:/newxamp/htdocs">
AllowOverride all
</Directory>
</VirtualHost>
```

- You're all done. You should be able to access api.ushahidi.test now and see the default API response
