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
        if ($multisite && !$request->isMethod('get') && service('site.config')['tier'] === 'demo') {
            $expiration_date = strtotime(service('site.config')['expiration_date']);
            $extension_date = strtotime(service('site.config')['extension_date']);
            $now = new DateTime();
            if ($expiration_date < $now && (!$extension_date || $extension_date < $now)) {
                abort(503, 'The demo period for this deployment has expired.');
            }
        }
        return $next($request);
    }
}
