<?php

namespace Ushahidi\App\Http\Middleware;

use Closure;

class AddContentLength
{
    /**
     * Add content-length header to responses before being sent.
     * This only happens if it hasn't been already set.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!$response->isEmpty() && !$response->headers->get('Content-Length')) {
            $response->headers->set(
                'Content-Length',
                // ensure that we get byte count by using 8bit encoding
                mb_strlen($response->getContent(), '8bit')
            );
        }
        return $response->prepare($request);
    }
}
