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

- Dependencies changes:

```
"illuminate/redis": "5.7.*"
"illuminate/mail": "5.7.*"
"laravel/lumen-framework": "5.7.*"
"laravel/passport": "7.3.1"
```

- `public/svg` directory must exist.
- `storage/framework/cache/data` must exist.
- `Call to undefined method Laravel\Lumen\Application::configurationIsCached()` issue [Passport issue in github](https://github.com/dusterio/lumen-passport/issues/106), fixed by setting `laravel/passport` to `7.3.1`

# 5.7 to 5.8
[Lumen doc](https://lumen.laravel.com/docs/5.8/upgrade#upgrade-5.8.0)

- Dependencies changes:

```
"vlucas/phpdotenv": "~3.0"
"illuminate/redis": "5.8.*"
"illuminate/mail": "5.8.*"
"laravel/lumen-framework": "5.8.*"
```

- Replaced `array_pluck` by `Arr:pluck`
- Replaced `array_flatten` by `Arr:flatten`
- Replaced `str_random` by `Str:random`
- Replaced `str_slug` by `Str:slug`
- Changed Cache class methods usage to consider seconds instead of minutes
- TODO: ClearDB for heroku won't work [unless we stop needing putenv()](https://laravel.com/docs/5.8/upgrade#deferred-service-providers). Affected code:

    ```
    // Parse ClearDB URLs
    if (getenv("CLEARDB_DATABASE_URL")) {
        $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
        // Push url parts into env
        putenv("DB_HOST=" . $url["host"]);
        putenv("DB_USERNAME=" . $url["user"]);
        putenv("DB_PASSWORD=" . $url["pass"]);
        putenv("DB_DATABASE=" . substr($url["path"], 1));
    }
    ```

- Updated `bootstrap/app.php`, `phinx.php` and `app/PlatformVerifier/Env.php` to use new syntax of `Dotenv` library: `Dotenv::create()`.
- Updated the `setUp` method so that its return type is `void` in the `ushahidi/platform/tests/unit` directory.
- Updated the `tearDown` method so that its return type is `void` in the `ushahidi/platform/tests/unit` directory.

# 5.8 to 6.0

```
    "illuminate/redis": "6.0.*"
    "illuminate/mail": "6.0.*"
    "laravel/lumen-framework": "^6.0"
    "sentry/sentry-laravel": "^1.2.0"
    "laravel/passport": "^9.3.2"
```

- Replaced `translator->trans()` method in `KohanaValidationEngine`, `ValidatorTrait` and `ContactRepository` by translator->get()` method as `Illuminate\Contracts\Translation\Translator` changed.
- Replaced `Input::get` by `Request::get` in `Category.php` and `Survey.php` as the `Illuminate\Support\Facades\Input` facade has been removed.
- Updated `create` method in `platform/app/Passport/ClientRepository.php` to be compatible with new Passport version. It now matches `platform/vendor/laravel/passport/src/ClientRepository.php`
- Updated `createPasswordGrantClient` method in `platform/app/Passport/ClientRepository.php` to be compatible with new Passport version. It now matches `platform/vendor/laravel/passport/src/ClientRepository.php`
- TODO: The default Redis client has changed from `predis` to `phpredis`. In order to keep using `predis`, ensure the redis.client configuration option is set to predis in `config/database.php` configuration file.
- TODO: germanazo/laravel-ckan-api does not support laravel 6.0, we need to fork it or replace the library. By now, the dependency was removed.
- TODO: Check sentry configuration, sentry-laravel version was updated and config is no longer compatible. Opened issue in sentry-laravel repo: https://github.com/getsentry/sentry-laravel/issues/409


