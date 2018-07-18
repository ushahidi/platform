<?php

namespace Ushahidi\App\Http\Middleware;

use Ushahidi\Core\Tool\Verifier;
use Closure;

class SignatureAuth
{

    protected $verifier;

    public function __construct(Verifier $verifier)
    {
        $this->verifier = $verifier;
    }

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

        if (!$this->verifier->verified(
            $signature,
            $api_key,
            $shared_secret,
            $fullURL,
            $request->getContent()
        )) {
            abort(403, "Forbidden.");
        }

        return $next($request);
    }
}
