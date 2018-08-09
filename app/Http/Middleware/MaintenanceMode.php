<?php

namespace Ushahidi\App\Http\Middleware;

use Closure;

class MaintenanceMode
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
        $maintenanceMode = env('MAINTENANCE_MODE');
        if ($maintenanceMode) {
            $maintenanceMessage = 'This site is down for maintenance';
            if (service('site.config')) {
                $maintenanceMessage = service('site.config')['name'] . ' is down for maintenance.';
            }
            abort(
                503,
                $maintenanceMessage
            );
        }

        return $next($request);
    }
}
