<?php

namespace Ushahidi\App\Repository\Concerns;

use Illuminate\Support\Facades\Cache;
use Ushahidi\Core\Entity;

trait CachesData
{

    /**
     * Cache lifetime in minutes
     */
    protected $cache_lifetime = 1;

    // CreateRepository
    // ReadRepository
    // UpdateRepository
    // DeleteRepository
    public function get($id, $fresh = false)
    {

        $resource = $this->getEntity()->getResource();
        $key = "$resource.$id";

        // If `fresh` then wipe cached result first
        if ($fresh) {
            Cache::forget($key);
        }

        return Cache::tags([$resource])->remember($key, $this->cache_lifetime, function () use ($id) {
            return parent::get($id);
        });
    }

    // UpdateRepository
    public function update(Entity $entity)
    {
        Cache::forget($entity->getResource() . '.' . $entity->getId());

        return parent::update($entity);
    }

    // DeleteRepository
    public function delete(Entity $entity)
    {
        Cache::forget($entity->getResource() . '.' . $entity->getId());

        return parent::delete($entity);
    }

    /**
     * Check if an entity with the given id exists
     * @param  int $id
     * @return bool
     */
    public function exists($id)
    {
        $resource = $this->getEntity()->getResource();
        $key = "$resource.$id";

        // If the record is cached, it exists obviously
        if (Cache::has($key)) {
            return true;
        }

        // @todo how can we cache this?!
        return (bool) $this->selectCount([
            $this->getTable().'.id' => $id
        ]);
    }
}
