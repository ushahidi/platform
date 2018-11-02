<?php

namespace Ushahidi\App\Multisite;

use Closure;

class DetectSiteMiddleware
{

    // @todo grab config in __construct??

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$features
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If we're not running in multsite mode...
        if (!$multsite->enabled()) {
            // ... just continue with the request
            return $next($request);
        }

        try {
            $multisite->setSiteFromHost($request->getHost());
        } catch (SiteNotFoundException $e) {
            abort(404, "Deployment not found");
        }

        $deployment = $multisite->getSite();

        // If the deployment hasn't been deployed yet
        if ($site->getStatus() === 'pending') {
            abort(503, $site->getName() . " is not ready");
        }

        // If the site is down for maintenance
        if ($site->getStatus() === 'maintenance') {
            abort(503, $site->getName() . " is down for maintenance");
        }

        // Finally, confirm the db is ready
        if (!$site->isDbReady()) {
            abort(503, $site->getName() . " is not ready");
        }

        // Otherwise just continue with the request
        return $next($request);
    }
}
