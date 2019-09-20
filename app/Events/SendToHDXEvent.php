<?php
namespace Ushahidi\App\Events;

use Log;

class SendToHDXEvent extends Event
{
    public $jobId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }
}
