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
        Log::debug('You fired a SendToHDXEvent for job: '.print_r($jobId, true));
        $this->jobId = $jobId;
    }
}
