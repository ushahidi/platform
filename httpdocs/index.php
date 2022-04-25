<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Test for gateway check mode
|--------------------------------------------------------------------------
|
| If the gwcheck query parameter is present in the query parameters and
| its value is truthy (checks out with TRUE in a loose comparsion), we enter
| gateway check mode. In this mode, the entrypoint returns information about
| the request as received from the web server. An operator or an automated
| tool on the other side can then contrast the responses with expectations,
| in order to determine if the web server and gateway interface are properly
| set up.
|
*/
if ($_REQUEST['gwcheck'] ?? null) {
    require __DIR__.'/../bootstrap/gwcheck.php';
    // the script above is expected to terminate execution
    // but just to make double sure:
    exit();
}

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need the Aura DI container, so let us turn on the lights.
| This bootstraps the dependencies and gets it ready for use.
|
*/

require_once __DIR__.'/../bootstrap/init.php';

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| First we need to get an application instance. This creates an instance
| of the application / container and bootstraps the application so it
| is ready to receive HTTP / Console requests from the environment.
|
*/

$app = require __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
