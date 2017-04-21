<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

// $app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Ushahidi\App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Ushahidi\App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//    Ushahidi\App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => Ushahidi\App\Http\Middleware\Authenticate::class,
// ]);
$app->routeMiddleware([
    'auth' => Ushahidi\App\Http\Middleware\Authenticate::class,
	'cors'   => Ushahidi\App\Http\Middleware\CorsMiddleware::class,
    'scopes' => Laravel\Passport\Http\Middleware\CheckScopes::class,
    'scope'  => Laravel\Passport\Http\Middleware\CheckForAnyScope::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(Ushahidi\App\Providers\AppServiceProvider::class);
$app->register(Ushahidi\App\Providers\AuthServiceProvider::class);
// $app->register(Ushahidi\App\Providers\EventServiceProvider::class);
$app->register(Ushahidi\App\Providers\PassportServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'Ushahidi\App\Http\Controllers'], function ($app) {
    require __DIR__.'/../routes/web.php';
});

return $app;
