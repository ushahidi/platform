# File Driver

The [Auth::File] driver is included with the auth module.

Below are additional configuration options that can be set for this driver.

Name | Type | Default | Description
-----|------|---------|-------------
users | `array` | array() | A user => password (_hashed_) array of all the users in your application

## Forcing Login

[Auth_File::force_login] allows you to force a user login without a password.

~~~
// Force the user with a username of admin to be logged into your application
Auth::instance()->force_login('admin');
$user = Auth::instance()->get_user();
~~~
