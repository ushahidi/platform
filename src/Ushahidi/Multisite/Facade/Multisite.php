<?php

namespace Ushahidi\Multisite\Facade;

use Illuminate\Support\Facades\Facade;

class Multisite extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'multisite';
    }
}
