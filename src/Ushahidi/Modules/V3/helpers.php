<?php

// TODO: maybe we should swap this for the standard Route::resource()
//       (not clear yet what it does differently)
if (!function_exists('resource')) {
    // Helper to generate routes similar to laravels Route::resource()
    function resource($router, $uri, $controller, array $options = [])
    {
        // Get id pattern from options, or use default
        $id = isset($options['id']) ? $options['id'] : 'id:[0-9]+';

        // Build list of methods to register routes for
        $methods = $defaults = ['index', 'store', 'show', 'update', 'destroy'];
        if (isset($options['only'])) {
            $methods = array_intersect($defaults, (array) $options['only']);
        } elseif (isset($options['except'])) {
            $methods = array_diff($defaults, (array) $options['except']);
        }

        // prefix for the routes
        $options['as'] = $options['as'] ?? str_replace('/', '.', trim($uri, ' ./'));

        // Finally register the routes
        $router->group([
            'prefix' => $uri,
        ] + $options, function () use ($router, $id, $methods, $controller) {
            if (in_array('index', $methods)) {
                $router->get('/', ['as' => 'index', 'uses' => $controller . '@index']);
            }

            if (in_array('store', $methods)) {
                $router->post('/', ['as' => 'store', 'uses' => $controller . '@store']);
            }

            if (in_array('show', $methods)) {
                $router->get('/{' . $id . '}', ['as' => 'show', 'uses' => $controller . '@show']);
            }

            if (in_array('update', $methods)) {
                $router->put('/{' . $id . '}', ['as' => 'update', 'uses' => $controller . '@update']);
            }

            if (in_array('destroy', $methods)) {
                $router->delete('/{' . $id . '}', ['as' => 'destroy', 'uses' => $controller . '@destroy']);
            }
        });
    }
}

/*
 * Utility function to add cache control middleware to a route
 *
 * It takes a single parameter to specify the minimum level of caching
 * settings that should activate caching behavior for this route.
 * See config/api.php for the defined levels
 */
if (!function_exists('add_cache_control')) {
    function add_cache_control(string $route_level)
    {
        /*
         * We are choosing not to cache responses to authenticated requests for the moment,
         * focusing only on guest requests.
         *
         * In order to implement this, two middleware instantiations of cache headers are layered.
         * The first one (bottom first) adds the default caching headers. The second middleware
         * (top in the list) applies only to logged in users and sets cache forbidding values in
         * the cache-control header.
         */
        return [
            // These are parsed bottom first, so the default is to cache (if the config allows it)
            "cache.headers.ifAuth:{$route_level},api,preset/dont-cache",
            // applies to api-authenticated requests
            "cache.headers.ifAuth:{$route_level},,preset/default",
        ];
    }
}


if (!function_exists('camel_case')) {
    /**
     * Convert a string to camel case.
     */
    function camel_case(string $string)
    {
        return \Illuminate\Support\Str::camel($string);
    }
}
