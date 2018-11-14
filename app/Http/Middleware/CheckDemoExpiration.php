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
        $isDemoTier = service('site.config')['tier'] === 'demo_1';
        $isNotGet = !$request->isMethod('get');

        if ($multisite && $isNotGet && $isDemoTier) {
            $now = strtotime('now');
            $config = service('site.config');
            if ($config) {
                $expiration_date = array_key_exists('expiration_date', $config) ?
                    strtotime($config['expiration_date']) : null;
                $extension_date = array_key_exists('extension_date', $config) ?
                    strtotime($config['extension_date']) : null;
                
                if ($expiration_date < $now && (!$extension_date || $extension_date < $now)) {
                    abort(503, 'The demo period for this deployment has expired.');
                }
            }
        }
        return $next($request);
    }
}
