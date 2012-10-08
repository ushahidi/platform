# Configuration

The default configuration file is located in `MODPATH/auth/config/auth.php`. You should copy this file to `APPPATH/config/auth.php` and make changes there, in keeping with the [cascading filesystem](../kohana/files).

[Config merging](../kohana/config#config-merging) allows these default configuration settings to apply if you don't overwrite them in your application configuration file.

Name | Type | Default | Description
-----|------|---------|------------
driver | `string` | file | The name of the auth driver to use.
hash_method | `string` | sha256 | The hashing function to use on the passwords.
hash_key | `string` | NULL | The key to use when hashing the password.
session_type | `string` | [Session::$default] | The type of session to use when storing the auth user.
session_key | `string` | auth_user | The name of the session variable used to save the user.
