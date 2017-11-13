<?php

namespace Ushahidi\App\Http\Middleware;

use Laravel\Passport\Http\Middleware\CheckScopes as PassportCheckScopes;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Exceptions\MissingScopeException;

class CheckScopes extends PassportCheckScopes
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$scopes
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Laravel\Passport\Exceptions\MissingScopeException
     */
    public function handle($request, $next, ...$scopes)
    {
        // If the request has any Auth headers...
        if ($request->headers->has('Authorization')) {
            // ... pass to the parent class to validate scopes
            return parent::handle($request, $next, ...$scopes);
        } else {
            // ... otherwise continue
            return $next($request);
        }
    }
}
