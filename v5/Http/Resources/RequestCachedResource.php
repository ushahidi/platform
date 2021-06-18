<?php
namespace v5\Http\Resources;

use Illuminate\Support\Collection;

trait RequestCachedResource
{

    // TO BE DEPRECATAED
    // This is a temporary device to speed up rendering of bodies that
    // currently involve frequent repeated operations against the database.
    // Specially against categories.
    // The better general approach is to preload category models in memory
    // and access them directly from there. To be implemented at this point.

    // Ensures and obtains the cache collection for the specific resource class.
    // This all is saved in the request context, so it's discarded after
    // the request has finished processing.
    protected function getCollectionCache($request)
    {
        if (!property_exists($request, '_cache_api_resources_arrays')) {
            $c0 = new Collection();
            $request->_cache_api_resources_arrays = $c0;
        } else {
            $c0 = $request->_cache_api_resources_arrays;
        }
        $class_name = static::class;
        if (!$c0->has($class_name)) {
            $c1 = new Collection();
            $c0->put($class_name, $c1);
        } else {
            $c1 = $c0->get($class_name);
        }
        return $c1;
    }

    // Resolves object by looking up in the request cache first,
    // only calling the resolver if it's not found.
    protected function cacheResolve($request, callable $resolve)
    {
        $c = $this->getCollectionCache($request);
        $resolved = $c->get($this->id);
        if (!$resolved) {
            $resolved = $resolve($request);
            $c->put($this->id, $resolved);
        }
        return $resolved;
    }

    //
    public function resolve($request = null)
    {
        if ($request == null) {
            return parent::resolve($request);
        }
        
        return $this->cacheResolve($request, function ($request) {
            return parent::resolve($request);
        });
    }
}
