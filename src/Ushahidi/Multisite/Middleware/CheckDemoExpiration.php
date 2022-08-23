<?php

namespace Ushahidi\Multisite\Middleware;

use Closure;
use Ushahidi\Multisite\MultisiteManager;

class CheckDemoExpiration
{
    /**
     * @var \Ushahidi\Multisite\MultisiteManager;
     */
    protected $multisite;

    /**
     * Create a new middleware instance.
     *
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
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If multisite is disabled, skip entirely
        if (! $this->multisite->enabled()) {
            return $next($request);
        }

        // If request is get, skip entirely
        if ($request->isMethod('get')) {
            return $next($request);
        }

        $site = $this->multisite->getSite();
        $isDemoTier = $site->tier === 'demo';

        if ($isDemoTier) {
            $now = time();
            // Move time conversion to Site model
            $expiration_date = strtotime($site->expiration_date);
            $extension_date = strtotime($site->extension_date);

            if ($expiration_date < $now && (! $extension_date || $extension_date < $now)) {
                abort(503, 'The demo period for this deployment has expired.');
            }
        }

        return $next($request);
    }
}
