<?php

namespace Ushahidi\Modules\V3\Listener;

use Illuminate\Support\Facades\Log;
use Ushahidi\Modules\V3\Events\SendToHDXEvent;

class SendToHDXEventListener
{
    /**
     * Handle the event.
     *
     * @param  SendToHDXEvent  $event
     * @return void
     */
    public function handle(SendToHDXEvent $event)
    {
        Log::debug('Received a SendToHDXEvent', [$event]);

        // Initiate the process to send data to HDX
        //confirm that we want to send HDX info
        $usecaseFactory = service('factory.usecase')
            ->get('hxl_send', 'send')
            ->setIdentifiers(['job_id' => $event->jobId])
            ->interact();
    }
}
