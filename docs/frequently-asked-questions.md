# Frequently Asked Questions

## Content

[Installation](frequently-asked-questions.md#installation)

[Configuration](frequently-asked-questions.md#configuration)

[Platform API](frequently-asked-questions.md#platform-api)

[Platform Client](frequently-asked-questions.md#platform-client)

[Mobile Application](frequently-asked-questions.md#mobile-application)

[General](frequently-asked-questions.md#general)

## Installation

### Which installation option should I choose?

As with many things, it depends.

**Are you are planning on developing new code for Ushahidi, or testing the platform?** Use one of the development setups.

* If you are already familiar with XAMPP and want to avoid using Vagrant for performance or familiarity issues, then go with the [XAMPP Install guide](https://docs.ushahidi.com/platform-developer-documentation/getting-started/setup_alternatives/xampp). 
* If you are familiar with Vagrant, or willing to learn how to setup Vagrant + Homestead and you have a machine capable of running a virtual machine and vagrant, then go with [the vagrant based setup](https://docs.ushahidi.com/platform-developer-documentation/getting-started/setup_alternatives/vagrant-setup) \(This is the setup Ushahidi staff uses on a daily basis!\)
* If you are a frontend developer that wants to contribute without setting up the backend, then try [setting up the platform client](https://docs.ushahidi.com/platform-developer-documentation/getting-started/setup_alternatives/setting-up-the-platform-client) only, and using the API route of an ushahidi.io deployment.  This the fastest way to get started.

**Are you ready to deploy Ushahidi for others to use?**

* if you plan to modify the code in some way, follow the [Installing for production environments setup guide.](https://docs.ushahidi.com/platform-developer-documentation/getting-started/setup_alternatives/installing-for-production-environments)
* If you want to deploy Ushahidi without worrying about upgrading, monitoring services, or hosting it yourself, start a new deployment in [http://ushahidi.io/create](http://ushahidi.io/create) and we will manage all the tech for you.
* If you're looking for something in between, [Contact the Ushahidi team ](http://gitter.im/ushahidi/Community)and we'll figure it out! We're always happy to chat.

### How do I install Ushahidi?

A step by step guide on how to install Ushahidi can be found [here](https://www.ushahidi.com/support/install-ushahidi).

### Can I install the Platform API?

Yes you can. You can follow this link for more resources on how to install: [here](https://docs.ushahidi.com/platform-developer-documentation/tech-stack/api-documentation).

### Do you support Windows installations?

Some folks have been successful in getting the platform set up for development in a windows environment. If you are not familiar with Vagrant or Vagrant is not working correctly for you in Windows, we recommend you try following the [XAMPP installation guide here](https://docs.ushahidi.com/platform-developer-documentation/getting-started/setup_alternatives/xampp) and let us know if you run into any issues.

For production environments, we strongly recommend you use a Linux based environment instead, since most of our development and all our production setup is done in either Mac OS or a Linux based distribution like Ubuntu, RedHat OS, Fedora, etc.

### When I run this command: `./bin/phinx migrate -c application/phinx.php` I get an error of "application/phinx.php does not exist" How do I solve this?

* First check if you in the root directory of the platform install. If not,  go inside the platform installation directory and try again.
* Check which version of the platform you are running. When you run `ls application/phinx.php` do you see this file, or an error? If you don't have an `application` directory in the top level of your platform install directory, then you are likely in version 4 of platform and should be running `php artisan migrate` instead. 

### All I do is get an ugly page with text that looks like code. What do I do?

You have installed only the API. Unless you are installing the bundle that contains both client and API, you would need to set up both separately. This also requires you to set up two different virtual hosts under different subdomains or ports. If this sounds too complicated, please look at the simplified install of the bundle.

### When I open a URL with a path other than / \(i.e. /views/map \) I get a "404 not found" error.

Your web server setup is not handling the URLs as designed. There can be different reasons for this. If you are using Apache, please make sure that you have the directive “AllowOverride All” configured for the folder where you made your installation. If you are using nginx, please make sure that you are using our recommended configuration file.

## Configuration

### If I get an error-message saying "Something went wrong, try reloading the page", what should I do?

First, let's identify your stack.

* Are you using ushahidi.io and running a deployment there? If YES, please contact our team through one of these channels: [https://www.ushahidi.com/contact\#](https://www.ushahidi.com/contact#)
* Are you a developer, setting up Ushahidi yourself? Start by checking the network tab in your development browser of choice and identifying any network errors. Look at the response for each and check what you see.

  The most common reasons for this error:

  * You have used the wrong url in the BACKEND\_URL key of your platform-client .ENV file. Check that when you access the URL in the browser + /api/v3/config it returns a valid json. It should look like this when you call the /api/v3/config endpoint for your API: [https://qa.api.ushahidi.io/api/v3/config](https://qa.api.ushahidi.io/api/v3/config) 
  * The server is failing for some reason. If the server is failing, it will likely show an error either in the browser when you call the API URL or in the server logs, which you can see in files contained within {the\_platform\_install\_dir}/storage/logs. Check the errors in the log, as often you will see that there is a permissions error somewhere, or a directory is missing, which you can solve yourself.

  When reporting issues, please note that we will need as much information as you can provide to be able to help you, so please start by checking all of the above, and then contact us with the information you found through[ the Ushahidi gitter](http://gitter.im/ushahidi/Community) channel. Including details about your development environment, what you have tried doing to solve it, what you were doing when this error occured, and your log files are critical in order for us to help you get set up.

* Are you a developer who is only setting up the client and using ushahidi.io for the API? This is most likely an error in your .ENV file, check that your BACKEND\_URL looks like this [http://DEPLOYMENTNAME.api.ushahidi.io](http://test.api.ushahidi.io) \(notice the .api after your deployment's name -- it's important!\)

### I have configured the datasources but I'm not getting any posts from them. What could be wrong?

#### Are you referring to an Ushahidi.io deployment?

Please verify that all the fields have the correct values and that you have enabled the "Accept survey submissions from this source" toggle \(it should be green/on!\)

![Example with disabled &quot;Twitter&quot; datasource.](.gitbook/assets/screen-shot-2019-08-03-at-11.08.49.png)

If you think your configuration is correct, please get in touch with your deploment name and details, and someone from the support team will be able to help.

#### Are you hosting Ushahidi yourself?

Please verify that all the fields have the correct values and that you have enabled the "Accept survey submissions from this source" toggle \(it should be green/on!\)

![Example with disabled &quot;Twitter&quot; datasource.](.gitbook/assets/screen-shot-2019-08-03-at-11.08.49.png)

If the configuration values are correct, then proceed to check the following in the platform API.

```text
php artisan datasource:incoming
```

Run the datasource:incoming task manually in the platform API directory \(as the example above\). It should succeed and not show any errors. If there are errors, check the logs under storage/logs to review what looks wrong. If nothing else, this will help you contact the team through[ the Ushahidi gitter](http://gitter.im/ushahidi/Community) channel with details.

If the incoming task worked, check if new posts are available. If they are, then this means that the problem is that the datasources work but are not being automatically fetched. Check that you have a crontab running periodically for your datasources and other tasks.

Open your crontab \(with `crontab -e`\) and check that it looks like this \(the path to platform may be different; it should point to your platform API installation directory\):

{% code title="crontab" %}
```bash
MAILTO=admin@example.com
 #ensure a valid email for system notifications
*/5 * * * * cd /var/www/platform && php artisan datasource:outgoing
*/5 * * * * cd /var/www/platform && php artisan datasource:incoming
*/5 * * * * cd /var/www/platform && php artisan savedsearch:sync
*/5 * * * * cd /var/www/platform && php artisan notification:queue

*/5 * * * * cd /var/www/platform && php artisan webhook:send
```
{% endcode %}

If after ensuring the crontab is correct and datasources run, you don't see any new posts, please get in touch through[ the Ushahidi gitter](http://gitter.im/ushahidi/Community) channel with all the details about what you tried and what you have seen, and we'll be happy to help.

### I am getting some sort of PHP error.

{% hint style="info" %}
Please ensure that you are using a supported version of PHP for the version of platform that you are running.
{% endhint %}

* **v2** supports up to PHP 5.4
* **v3** supports PHP 5.6 and 7.0
* **v4.0.0** supports PHP 7.0 to 7.2
* **v4.1.0+** supports PHP 7.1 to 7.3 \(inclusive\). This change was made to ensure we support versions of PHP that are getting security fixes at the very least. See PHP maintainance schedules [here](https://www.php.net/supported-versions.php).

### I’m getting a database connection error.

Please verify that you have created your MySQL database, know the correct credentials for the API to connect to it and that your “.env” file is created with the expected format and at the expected location

### How do I upgrade Ushahidi?

Please follow the step by step guide on how to update your deployment to the latest Ushahidi version [here](https://www.ushahidi.com/support/upgrading-ushahidi).

## Platform API

### Can I add a new datasource to the platform?

Yes, you can. You will need to fork and modify the Ushahidi platform API repository to do so. New datasource types need to be coded into the platform. Check out the `src/App/DataSource/` directory in the platform API codebase to learn how the current datasources are created.

If you are planning to add a new data source, please get in touch! The Ushahidi development team will be more than happy to help answer any questions or provide guidance.

## Platform Client

### How do I change the colours or appearance of the platform?

To change the color or appearance of platform, you will need to fork and modify the Ushahidi Pattern Library, and host Ushahidi in your own servers for the changes to be available. [Please follow this guide about our pattern library to learn more.](https://docs.ushahidi.com/platform-developer-documentation/changing-ui-styles-introduction-to-the-pattern-library)

## Mobile application

### How do I connect to the Ushahidi mobile application?

Please follow the step by step guide on how to connect to the Ushahidi mobile application [here](https://www.ushahidi.com/support/connecting-to-ushahidi-mobile-app).

## General

### How can I start contributing code to the platform?

TO contribute code to the Ushahidi platform, please follow the guidelines here: [step-by-step guide to adding code to the platform](https://www.ushahidi.com/support/add-code-to-ushahidi)

### How can I contribute to translations?

Translating the platform into different langauges helps us allow more people access to Ushahidi. You can help us translate the platform into as many languages as possible by following the instructions to start translating here: [Instructions on how to start translating](translation/software-localization-and-translation.md)

### How  can I join the Ushahidi Community?

Connect with the wider Ushahidi community:

* Join the discussion on [our forum](http://forums.ushahidi.com/)
* Sign up on the [mailing list](http://list.ushahidi.com/)
* Chat with us on:
  * IRC at [\#ushahidi on Freenode](http://irc//irc.freenode.net/#ushahidi)
  * Gitter at [ushahidi/Community](https://gitter.im/ushahidi/community)
  * [Skype](https://join.skype.com/S9t68IVKzwo8)
* Messages to any of these channels should show up on all of them!

### How to get help in a different language?

The Ushahidi community is global. If you need assistance in a different language, please [contact us](http://ushahidi.com/contact-us) and we will try connect you to a wonderful helper.

