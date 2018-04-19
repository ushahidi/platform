<?php

namespace Ushahidi\App\Http\Middleware;

use Closure;
use Ushahidi\Core\Tool\Verifier;

class SignatureAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_key = $request->input('api_key');
        $signature = $request->header('X-Ushahidi-Signature');
        $shared_secret = getenv('PLATFORM_SHARED_SECRET');
        $fullURL = $request->fullUrl();

        $verifier = new Verifier($signature, $api_key, $shared_secret, $fullURL, $request->getInputSource()->all());

        if (!$verifier->verified()) {
            abort(403, "Forbidden.");
        }

        return $next($request);
    }
}
