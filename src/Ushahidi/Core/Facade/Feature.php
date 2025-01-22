<?php

namespace Ushahidi\Core\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ushahidi\Core\Tool\FeatureManager
 */
class Feature extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'feature';
    }
}
