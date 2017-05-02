<?php

namespace Ushahidi\App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
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
        if ($this->auth->guard($guard)->guest()) {
            // Throw a 401 error here so that its passed to the error handler and formatter
            // Hard coding WWW-Authenticate because this isn't handle properly by passport
            abort(401, "Unauthorized.", ['WWW-Authenticate' => 'Bearer realm="OAuth"']);

            // return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
