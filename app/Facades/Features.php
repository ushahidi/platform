<?php

namespace Ushahidi\App\Facades;

use Illuminate\Support\Facades\Facade;

class Features extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'features';
    }
}
