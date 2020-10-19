<?php

namespace Ushahidi\App\Http\Middleware;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
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
     * Check if configuration enables caching for this route.
     * Return resolved configuration or null if not enabled.
     */
    protected function checkConfig($route_level)
    {
        // caching levels that we recognize
        $levels = [ 'off', 'minimal' ];

        // fetch config
        $cfg = [
            'level' => config('routes.cache_control.level'),
            'max_age' => config('routes.cache_control.max_age'),
            'private_only' => config('routes.cache_control.private_only'),
        ];

        // translate level tags to numbers
        $cfg_level_n = array_search($cfg['level'], $levels);
        $r_level_n = array_search($route_level, $levels);
        if ($cfg_level_n === false || $r_level_n === false) {
            // there's some misconfiguration ...
            if ($cfg_level_n === false) {
                Log::warn('Unrecognized cache control level in config', [$cfg['level']]);
            } else {
                Log::warn('Unrecognized cache control level in route config', [$route_level]);
            }
            // so don't indicate caching
            return null;
        }

        if ($cfg_level_n >= $r_level_n) {
            return $cfg;
        } else {
            return null;
        }
    }

    /**
     * Add cache related HTTP headers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $level           cache level assigned to the route, specify the minimum
     *                                  level of caching settings that should activate caching
     *                                  behavior for this route.
     *                                  See config/api.php for the defined levels
     * @param  string  $ifAuth          authentication guard that should be satisfied
     * @param  string|array  $options   cache options. Special presets:
     *                                  'preset/dont-cache' -> no-store
     *                                  'preset/default' -> default cache settings as per config
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \InvalidArgumentException
     */
    public function handle($request, Closure $next, $level, $ifAuth = null, $options = [])
    {
        $response = $next($request);

        // If this is not cacheable stuff, bail out
        if (! $request->isMethodCacheable() || ! $response->getContent()) {
            return $response;
        }

        // Check config and wether it enables caching
        $cfg = $this->checkConfig($level);
        if ($cfg == null) {
            return $response;
        }

        // If the cache settings are guarded by auth, check if the auth satisfies
        if ($ifAuth != null) {
            if ($this->auth->guard($ifAuth)->guest()) {
                return $response;
            }
        }

        // Check if options is a preset
        if (is_string($options) && strpos(strtolower($options), 'preset/') === 0) {
            $preset = strtolower(explode('/', $options)[1] ?? "");
            if ($preset === 'dont-cache') {
                $options = 'no_store';
            } elseif ($preset === 'default') {
                $viz = $cfg['private_only'] ? 'private' : 'public';
                $maxage = $cfg['max_age'];
                $options = [ $viz => true, 'max_age' => "${maxage}" ];
            } else {
                // Unrecognized preset , don't cache
                Log::warn('Unrecognized cache options preset', [$preset]);
                return $response;
            }
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
