<?php

namespace Ushahidi\Multisite;

use Closure;

class DetectSiteMiddleware
{
    /**
     * @var \Ushahidi\Multisite\MultisiteManager;
     */
    protected $multisite;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(MultisiteManager $multisite)
    {
        $this->multisite = $multisite;
    }

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
        if (!$this->multisite->enabled()) {
            // ... just continue with the request
            return $next($request);
        }

        try {
            $this->multisite->setSiteFromHost($request->getHost());
        } catch (SiteNotFoundException $e) {
            abort(404, "Deployment not found");
        }

        $site = $this->multisite->getSite();

        // If the deployment hasn't been deployed yet
        if ($site->getStatus() === 'pending') {
            abort(503, "Your deployment is not ready yet. Please try again later.");
        }

        // If the site is down for maintenance
        if ($site->getStatus() === 'maintenance') {
            abort(503, "The deployment is down for maintenance.");
        }

        // Finally, confirm the db is ready
        if (!$site->isDbReady()) {
            abort(503, "Your deployment is not ready yet. Please try again later.");
        }

        // Otherwise just continue with the request
        return $next($request);
    }
}
