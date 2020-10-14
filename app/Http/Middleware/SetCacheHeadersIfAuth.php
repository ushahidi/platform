<?php

namespace Ushahidi\App\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Auth\Factory as Auth;

class SetCacheHeadersIfAuth
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
     * Add cache related HTTP headers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $ifAuth
     * @param  string|array  $options
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \InvalidArgumentException
     */
    public function handle($request, Closure $next, $ifAuth = null, $options = [])
    {
        $response = $next($request);

        if ($ifAuth != null) {
            if ($this->auth->guard($ifAuth)->guest()) {
                return $response;
            }
        }

        if (! $request->isMethodCacheable() || ! $response->getContent()) {
            return $response;
        }

        if (is_string($options)) {
            $options = $this->parseOptions($options);
        }

        if (isset($options['etag']) && $options['etag'] === true) {
            $options['etag'] = md5($response->getContent());
        }

        if (isset($options['last_modified'])) {
            if (is_numeric($options['last_modified'])) {
                $options['last_modified'] = Carbon::createFromTimestamp($options['last_modified']);
            } else {
                $options['last_modified'] = Carbon::parse($options['last_modified']);
            }
        }

        $response->headers->remove('cache-control');
        // Still not available in symfony's setCache() method
        if (isset($options['no_cache'])) {
            $response->headers->set('Cache-Control', 'no-cache');
            unset($options['no_cache']);
        } elseif (isset($options['no_store'])) {
            $response->headers->set('Cache-Control', 'no-store');
            unset($options['no_store']);
        }
        $response->setCache($options);
        $response->setVary('Authorization', false);
        $response->isNotModified($request);

        return $response;
    }

    /**
     * Parse the given header options.
     *
     * @param  string  $options
     * @return array
     */
    protected function parseOptions($options)
    {
        return collect(explode(';', $options))->mapWithKeys(function ($option) {
            $data = explode('=', $option, 2);

            return [$data[0] => $data[1] ?? true];
        })->all();
    }
}
