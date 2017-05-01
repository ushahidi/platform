<?php

namespace Ushahidi\App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Ushahidi\App\Events\SomeEvent' => [
            'Ushahidi\App\Listeners\EventListener',
        ],
    ];
}
