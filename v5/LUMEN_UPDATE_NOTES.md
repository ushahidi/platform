#5.5 to 5.6

- Added config/hashing.php
- Added config/logging.php
- Several vendor versions were updated in composer.json file:

 ```
 "illuminate/mail": "5.6.*"
 "illuminate/redis": "5.6.*"
 "laravel/passport": "^6.0"
 "laravel/lumen-framework": "5.6.*"
 "robmorgan/phinx": "~0.10.0"
 "phpspec/phpspec": "~5.0"
 "laravel/homestead": "~7.0"
 ```

- Issue with Passport config path, workaround applied:
 [Passport issue in github](https://github.com/dusterio/lumen-passport/issues/78)
- Note for dev: `COMPOSER_MEMORY_LIMIT=-1 composer install --verbose -vvv` might have to run composer without memory limit

#5.6 to 5.7
