# Developing Drivers

## Real World Example

Sometimes the best way to learn is to jump right in and read the code from another module. The [ORM](https://github.com/kohana/orm/blob/3.2/develop/classes/kohana/auth/orm.php) module comes with an auth driver you can learn from.

[!!] We will be developing an `example` driver. In your own driver you will substitute `example` with your driver name.

This example file would be saved at `APPPATH/classes/auth/example.php` (or `MODPATH` if you are creating a module).

---

## Quick Example

First we will show you a quick example and then break down what is going on.

~~~
class Auth_Example extends Auth
{
	protected function _login($username, $password, $remember)
	{
		// Do username/password check here
	}

	public function password($username)
	{
		// Return the password for the username
	}

	public function check_password($password)
	{
		// Check to see if the logged in user has the given password
	}

	public function logged_in($role = NULL)
	{
		// Check to see if the user is logged in, and if $role is set, has all roles
	}

	public function get_user($default = NULL)
	{
		// Get the logged in user, or return the $default if a user is not found
	}
}
~~~

## Extending Auth

All drivers must extend the [Auth] class.

	class Auth_Example extends Auth

## Abstract Methods

The `Auth` class has 3 abstract methods that must be defined in your new driver.

~~~
abstract protected function _login($username, $password, $remember);

abstract public function password($username);

abstract public function check_password($user);
~~~

## Extending Functionality

Given that every auth system is going to check if users exist and if they have roles or not you will more than likely have to change some default functionality.

Here are a few functions that you should pay attention to.

~~~
public function logged_in($role = NULL)

public function get_user($default = NULL)
~~~

## Activating the Driver

After you create your driver you will want to use it. It is a easy as setting the `driver` [configuration](config) option to the name of your driver (in our case `example`).
