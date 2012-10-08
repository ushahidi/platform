# Auth

User authentication and authorization is provided by the auth module.

The auth module is included with Kohana, but needs to be enabled before you can use it. To enable, open your `application/bootstrap.php` file and modify the call to [Kohana::modules] by including the auth module like so:

~~~
Kohana::modules(array(
	...
	'auth' => MODPATH.'auth',
	...
));
~~~

Next, you will then need to [configure](config) the auth module.

The auth module provides the [Auth::File] driver for you. There is also an auth driver included with the ORM module.

As your application needs change you may need to find another driver or [develop](driver/develop) your own.
