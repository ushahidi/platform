<?php

namespace Ushahidi\Core\Facade;

use Illuminate\Support\Facades\Facade;

class Site extends Facade
{
    /**
     * Get the registered name of the service.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'site';
    }
}
