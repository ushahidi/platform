<?php

namespace Ushahidi\App\Http\Middleware;

use Closure;

class CheckDemoExpiration
{

   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @param  string|null  $guard
    * @return mixed
    */
    public function handle($request, Closure $next, $guard = null)
    {
        $multisite = app('multisite');
        $site = $multisite->getSite();
        $isDemoTier = $site->tier === 'demo';
        $isNotGet = !$request->isMethod('get');

        if ($multisite->enabled() && $isNotGet && $isDemoTier) {
            $now = time();
            // Move time conversion to Site model
            $expiration_date = strtotime($site->expiration_date);
            $extension_date = strtotime($site->extension_date);

            if ($expiration_date < $now && (!$extension_date || $extension_date < $now)) {
                abort(503, 'The demo period for this deployment has expired.');
            }
        }
        return $next($request);
    }
}
