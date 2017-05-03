<?php

/**
 * Middleware to add CORS headers to every request
 *
 * @todo  move to shared module
 */

namespace Ushahidi\App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, \Closure $next)
    {
        // @todo move OPTIONS handling elsewhere
        if ($request->isMethod('OPTIONS')) {
            $response = response()->json(["method" => "OPTIONS"], 200);
        } else {
            $response = $next($request);
        }

        $response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Credentials', 'true');
        // @todo add 'Allow' header based on controller methods

        return $response;
    }
}
