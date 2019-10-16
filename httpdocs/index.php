<?php

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

$app->run();
