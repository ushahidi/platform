<?php

namespace Ushahidi\Core\Eloquent\Contracts;

interface Cacheable
{
    /**
     * Defines cache key.
     *
     * @return string
     */
    public function cacheKey(): string;

    /**
     * Removes cache for model.
     *
     * @param $model
     */
    public function invalidateCache($model): void;
}
