<?php

namespace Ushahidi\App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Ushahidi\App\Http\Middleware\TrimStrings::class,
        // \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Ushahidi\App\Http\Middleware\TrustProxies::class,
        \Ushahidi\App\Http\Middleware\AddContentLength::class,
        \Ushahidi\App\Multisite\DetectSiteMiddleware::class,
        \Barryvdh\Cors\HandleCors::class,
        \Ushahidi\App\Http\Middleware\MaintenanceMode::class,
        \Ushahidi\App\Http\Middleware\SetLocale::class,
        \Ushahidi\App\V5\Http\Middleware\V5GlobalScopes::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // \Ushahidi\App\Http\Middleware\EncryptCookies::class,
            // \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            // \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            // \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // \Ushahidi\App\Http\Middleware\VerifyCsrfToken::class,
            // \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // 'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        // 'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth' => \Ushahidi\App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \Ushahidi\App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'scopes' => \Ushahidi\App\Http\Middleware\CheckScopes::class,
        'scope'  => \Ushahidi\App\Http\Middleware\CheckForAnyScope::class,
        'expiration' => \Ushahidi\App\Http\Middleware\CheckDemoExpiration::class,
        'signature' => \Ushahidi\App\Http\Middleware\SignatureAuth::class,
        'feature' => \Ushahidi\App\Http\Middleware\CheckFeature::class,
        'invalidJSON' => \Ushahidi\App\Http\Middleware\CheckForInvalidJSON::class,
        'cache.headers.ifAuth' => \Ushahidi\App\Http\Middleware\SetCacheHeadersIfAuth::class
    ];
}
