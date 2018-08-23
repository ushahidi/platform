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
        $multisite = config('multisite.enabled');
        $isDemoTier = service('site.config')['tier'] === 'demo';
        $now = new DateTime();
        $expiration_date = strtotime(service('site.config')['expiration_date']);
        $extension_date = strtotime(service('site.config')['extension_date']);
        $isNotGet = !$request->isMethod('get');

        if ($multisite && $isNotGet && $isDemoTier) {
            if ($expiration_date < $now && (!$extension_date || $extension_date < $now)) {
                abort(503, 'The demo period for this deployment has expired.');
            }
        }
        return $next($request);
    }
}
