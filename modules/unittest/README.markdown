# Kohana-PHPUnit integration

This module integrates PHPUnit with Kohana.

If you look through any of the tests provided in this module you'll probably notice all theHorribleCamelCase.
I've chosen to do this because it's part of the PHPUnit coding conventions and is required for certain features such as auto documentation.

## Requirements

* [PHPUnit](http://www.phpunit.de/) >= 3.4

## Usage

	$ phpunit --bootstrap=modules/unittest/bootstrap.php modules/unittest/tests.php

Alternatively you can use a `phpunit.xml` to have a more fine grained control
over which tests are included and which files are whitelisted.

Make sure you only whitelist the highest files in the cascading filesystem, else
you could end up with a lot of "class cannot be redefined" errors.  

If you use the `tests.php` testsuite loader then it will only whitelist the
highest files. see `config/unittest.php` for details on configuring the
`tests.php` whitelist.
