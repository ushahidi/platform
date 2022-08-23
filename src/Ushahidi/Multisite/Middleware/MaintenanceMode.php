<?php

namespace Ushahidi\Multisite\Middleware;

use Closure;
use Ushahidi\Multisite\MultisiteManager;

class MaintenanceMode
{
    /**
     * @var \Ushahidi\Multisite\MultisiteManager;
     */
    protected $multisite;

    public function __construct(MultisiteManager $multisite)
    {
        $this->multisite = $multisite;
    }
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
        if (env('MAINTENANCE_MODE') == true) {
            $maintenanceMessage = 'This site is down for maintenance';
            if ($site = $this->multisite->getSite()) {
                $maintenanceMessage = $site->getName() . ' is down for maintenance.';
            }

            abort(
                503,
                $maintenanceMessage
            );
        }

        return $next($request);
    }
}
