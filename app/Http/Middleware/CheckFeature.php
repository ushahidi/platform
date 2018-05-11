<?php

namespace Ushahidi\App\Http\Middleware;

use Ushahidi\App\Facades\Features;
use Closure;

class CheckFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$features
     * @return mixed
     */
    public function handle($request, Closure $next, ...$features)
    {
        foreach ($features as $feature) {
            if (!Features::isEnabled($feature)) {
                abort(404);
            }
        }

        return $next($request);
    }
}
