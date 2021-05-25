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
"vlucas/phpdotenv": "^3.3"
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
[Lumen doc](https://lumen.laravel.com/docs/5.8/upgrade#upgrade-5.8.0)

- Dependencies changes:

```
    "illuminate/redis": "6.0.*"
    "illuminate/mail": "6.0.*"
    "laravel/lumen-framework": "^6.0"
    "sentry/sentry-laravel": "^1.2.0"
    "laravel/passport": "^9.3.2"
    "doctrine/dbal": "^2.0"
```

- Replaced `translator->trans()` method in `KohanaValidationEngine`, `ValidatorTrait` and `ContactRepository` by translator->get()` method as `Illuminate\Contracts\Translation\Translator` changed.
- Replaced `Input::get` by `Request::input` in `Category.php` and `Survey.php` as the `Illuminate\Support\Facades\Input` facade has been removed.
- Updated `create` method in `platform/app/Passport/ClientRepository.php` to be compatible with new Passport version. It now matches `platform/vendor/laravel/passport/src/ClientRepository.php`
- Updated `createPasswordGrantClient` method in `platform/app/Passport/ClientRepository.php` to be compatible with new Passport version. It now matches `platform/vendor/laravel/passport/src/ClientRepository.php`
- Replaced `League\OAuth2\Server\Exception\OAuthServerException` by `Laravel\Passport\Exceptions\OAuthServerException` in:
    - `app/Passport/TokenGuard.php`
    - `app/Exceptions/Handler.php`
    - As per [Passport documentation on renderable exceptions](https://github.com/laravel/passport/blob/master/UPGRADE.md#renderable-exceptions-for-oauth-errors)
- Changed `secret` field in `oauth_clients` table as per [Passport documentation on public clients](https://github.com/laravel/passport/blob/master/UPGRADE.md#public-clients): `migrations/20201110132445_update_oauth_clients_table_secret.php`
- Added `provider` field in `oauth_clients` table as per [Passport documentation on multiple guards](https://github.com/laravel/passport/blob/master/UPGRADE.md#support-for-multiple-guards): `migrations/20201110135500_update_oauth_clients_table_providers.php`
- `"doctrine/dbal": "^2.0"` dependency was added because it was needed to make changes in oauth tables
- TODO: The default Redis client has changed from `predis` to `phpredis`. In order to keep using `predis`, ensure the redis.client configuration option is set to predis in `config/database.php` configuration file.
- TODO: germanazo/laravel-ckan-api does not support laravel 6.0, we need to fork it or replace the library. By now, the dependency was removed.
- TODO: Check sentry configuration, sentry-laravel version was updated and config is no longer compatible. Opened issue in sentry-laravel repo: https://github.com/getsentry/sentry-laravel/issues/409
- TODO: Check [Passport credentials hashing and update env files](https://github.com/laravel/passport/blob/master/UPGRADE.md#client-credentials-secret-hashing)

# 6.0 to 7.0
[Lumen doc](https://lumen.laravel.com/docs/7.x/upgrade#upgrade-6.x)

- Dependencies changes:

```
"illuminate/redis": "7.0.*"
"illuminate/mail": "7.0.*"
"laravel/lumen-framework": "^7.0"
"sentry/sentry-laravel": "~2.1.0"
"fruitcake/laravel-cors": "^2.0"
"phpunit/phpunit": "^8.5"
"asm89/stack-cors": "~2.0"
"vlucas/phpdotenv": "^4.0"
"robmorgan/phinx": "~0.11.2"
"laravel/homestead": "~9.4.0"
"phpspec/phpspec": "~7.0"
"friends-of-behat/mink-extension": "^2.4"
"captainhook/captainhook": "^5.0"
"symfony/psr-http-message-bridge": "^2.0"
```

- The big deal here is that lumen now relies on symfony 5, so we had to check in all the dependencies how it was affected.
- Added `"symfony/psr-http-message-bridge": "^2.0"` as dependency (needed to add it manually for login to work), and modified `TokenGuard` to use `PsrHttpFactory` instead of `DiactorosFactory` (deprecated). More info [here](https://symfony.com/doc/current/components/psr7.html#converting-from-httpfoundation-objects-to-psr-7)
- Replaced `"barryvdh/laravel-cors"` by `"fruitcake/laravel-cors"`
- Replaced `Barryvdh\Cors\HandleCors::class` by ` Fruitcake\Cors\HandleCors::class` in `bootstrap/lumen.php`
- Removed `"phpunit/dbunit": "~4.0.0"` depedency there is no compatible version with `"phpunit/phpunit": "^8.5"`. More on `dbunit` deprecation can be found [here](https://github.com/sebastianbergmann/dbunit/issues/217) and [here](https://www.reddit.com/r/PHP/comments/cbmzhw/has_dbunit_been_abandoned_what_are_you_guys_using/).
- Removed `"behat/mink-extension": "^2.2",` dependency, as it no longer supports symfony 5 (required by Lumen 7), it was replaced by
`"friends-of-behat/mink-extension": "^2.4"` which is a fork of the last one but with symfony 5 support
- Replaced `"sebastianfeldmann/captainhook"` by `"captainhook/captainhook"`
- Updated `tests/CaptainHook/PHPCS.php` and `captainhook.json` to be compatible with new captainhook version
- Make sure hooks are installed by running `bin/captainhook install`
- Updated `report` and `render` methods in `app/Exceptions/Handler.php` so that they take `Throwable` instead of `Exception` as parameters
- Updated `(Dotenv::create(...))->load();` by
```
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
 ```
in the files: `app/PlatformVerifier/Env.php`, `bootstrap/app.php`, and `phinx.php`
- Changed `config/cors.php` properties casing to snake_case due to [Laravel cors dependency update](https://github.com/fruitcake/laravel-cors#upgrading-from-0x--barryvdh-laravel-cors)
- Added: `$this->setName('migrate');` to `app/Console/Commands/MigrateCommand.php`
- Removed `syntaxCheck="false"` from `/Users/arelsirin/dev/ushahidi/platform/phpunit.xml.dist` due to phpunit update
- Added `return 0;` to `handle()` methods in `src/Console/Command` classes to be compliant with symfony 5 console.
- TODO: Check more about [Laravel Cors update doc](https://github.com/fruitcake/laravel-cors#upgrading-from-0x--barryvdh-laravel-cors)

# Abandoned packages review

After 7.0 migration, the remaining abandoned packages are:

```
Package container-interop/container-interop is abandoned, you should avoid using it. Use psr/container instead.
Package guzzle/guzzle is abandoned, you should avoid using it. Use guzzlehttp/guzzle instead.
Package symm/gisconverter is abandoned, you should avoid using it. No replacement was suggested.
Package fzaninotto/faker is abandoned, you should avoid using it. No replacement was suggested.
Package phpunit/php-token-stream is abandoned, you should avoid using it. No replacement was suggested.
Package satooshi/php-coveralls is abandoned, you should avoid using it. Use php-coveralls/php-coveralls instead.

```

- `satooshi/php-coveralls` was replaced by `php-coveralls/php-coveralls`
- The rest are dependencies of other non-abandoned dependencies so we are not replaceing them yet.

- Removed repeat code snippets from `app/Exceptions/Handler.php` that exists already Lumen Exception Handler class
- Set `supports_credentials` value to true in cors.php
- Notes on changes to Passport https://jianjye.medium.com/how-to-fix-invalid-grant-error-with-laravel-passport-607ec923c8b3
- Update `authenticateViaBearerToken` method on App\Passport\TokenGuard to catch League OAuthServerException
