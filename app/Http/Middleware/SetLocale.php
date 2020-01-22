<?php
namespace Ushahidi\App\Http\Middleware;

use Closure;

class SetLocale
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
        $locale = $request->header('Accept-language');
        app('translator')->setLocale($locale);

        return $next($request);
    }
}
