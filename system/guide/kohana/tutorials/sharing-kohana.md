# Sharing Kohana

Because Kohana follows a [front controller] pattern, which means that all requests are sent to `index.php`, the filesystem is very configurable.  Inside of `index.php` you can change the `$application`, `$modules`, and `$system` paths.

[!!] There is a security check at the top of every Kohana file to prevent it from being accessed without using the front controller.  Also, the `.htaccess` file should protect those folders as well.  Moving the application, modules, and system directories to a location that cannot be accessed vie web can add another layer of security, but is optional.   

The `$application` variable lets you set the directory that contains your application files. By default, this is `application`. The `$modules` variable lets you set the directory that contains module files. The `$system` variable lets you set the directory that contains the default Kohana files. You can move these three directories anywhere.

For instance, by default the directories are set up like this:

    www/
        index.php
        application/
        modules/
        system/

You could move the directories out of the web root so they look like this:

    application/
    modules/
    system/
    www/
        index.php

Then you would need to change the settings in `index.php` to be:

    $application = '../application';
    $modules     = '../modules';
    $system      = '../system';

## Sharing system and modules

To take this a step further, we could point several kohana apps to the same system and modules folders.  For example (and this is just an example, you could arrange these anyway you want):

	apps/
		foobar/
			application/
			www/
		bazbar/
			application/
			www/
	kohana/
		3.0.6/
		3.0.7/
		3.0.8/
	modules/

And you would need to change the settings in `index.php` to be:

	$application = '../application';
	$system      = '../../../kohana/3.0.6';
	$modules     = '../../../kohana/modules';

Using this method each app can point to a central copy of kohana, and you can add a new version, and quickly update your apps to point to the new version by editing their `index.php` files.
