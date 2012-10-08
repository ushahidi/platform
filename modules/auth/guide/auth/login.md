# Log in and out

The auth module provides methods to help you log users in and out of your application.

## Log in

The [Auth::login] method handles the login.

~~~
// Handled from a form with inputs with names email / password
$post = $this->request->post();
$success = Auth::instance()->login($post['email'], $post['password']);

if ($success)
{
	// Login successful, send to app
}
else
{
	// Login failed, send back to form with error message
}
~~~

## Logged in User

There are two ways to check if a user is logged in. If you just need to check if the user is logged in use [Auth::logged_in].

~~~
if (Auth::instance()->logged_in())
{
	// User is logged in, continue on
}
else
{
	// User isn't logged in, redirect to the login form.
}
~~~

You can also get the logged in user object by using [Auth::get_user]. If the user is null, then no user was found.

~~~
$user = Auth::instance()->get_user();

// Check for a user (NULL if not user is found)
if ($user !== null)
{
	 // User is found, continue on
}
else
{
	// User was not found, redirect to the login form
}
~~~

## Log out

The [Auth::logout] method will take care of logging out a user.

~~~
Auth::instance()->logout();
// Redirect the user back to login page
~~~
