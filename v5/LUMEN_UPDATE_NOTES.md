# 5.5 to 5.6
[Lumen doc](https://lumen.laravel.com/docs/5.6/upgrade#upgrade-5.6.0)

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

- Issue with Passport config path, workaround applied in `app/helpers.php` file,
 [Passport issue in github](https://github.com/dusterio/lumen-passport/issues/78)
- Note for dev: `COMPOSER_MEMORY_LIMIT=-1 composer install --verbose -vvv` we might have to run composer without memory limit

# 5.6 to 5.7
[Lumen doc](https://lumen.laravel.com/docs/5.7/upgrade#upgrade-5.7.0)

- `public/svg` directory must exist.
- `storage/framework/cache/data` must exist.
- `Call to undefined method Laravel\Lumen\Application::configurationIsCached()` issue [Passport issue in github](https://github.com/dusterio/lumen-passport/issues/106), fixed by setting `laravel/passport` to `7.3.1`

# 5.7 to 5.8
[Lumen doc](https://lumen.laravel.com/docs/5.8/upgrade#upgrade-5.8.0)

- In progress.