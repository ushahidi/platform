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
        'Ushahidi\App\Events\SendToHDXEvent' => [
            'Ushahidi\App\Listeners\SendToHDXEventListener',
        ],
    ];

    /*
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        'Ushahidi\App\Subscriber',
    ];
}
