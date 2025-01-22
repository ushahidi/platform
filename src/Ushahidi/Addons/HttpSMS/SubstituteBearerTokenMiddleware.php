<?php

namespace Ushahidi\Addons\HttpSMS;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubstituteBearerTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Perform action
        $request->headers->set('X-Authorization', $request->bearerToken());
        $request->headers->remove('Authorization');

        return $next($request);
    }
}
