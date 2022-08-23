<?php

namespace Ushahidi\Core\Tool;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Ushahidi\Multisite\MultisiteAware;

abstract class Job implements ShouldQueue
{
    /*
    |--------------------------------------------------------------------------
    | Queueable Jobs
    |--------------------------------------------------------------------------
    |
    | This job base class provides a central location to place any logic that
    | is shared across all of your jobs. The trait included with the class
    | provides access to the "queueOn" and "delay" queue helper methods.
    |
    */

    use InteractsWithQueue, Queueable;

    // SerializesModels is included with MultisiteAware to handle the clash
    // of sleep/wakeup methods
    // use SerializesModels;

    use MultisiteAware;
}
