<?php

namespace Ushahidi\Multisite;

use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Ushahidi\Multisite\Facade\Multisite;

trait MultisiteAware
{

    /**
     * @var int The hostname ID of the previously active deployment
     */
    protected $site_id;

    use SerializesModels {
        __sleep as serializedSleep;
        __wakeup as serializedWakeup;
    }

    public function __sleep()
    {
        if (!$this->site_id) {
            $this->site_id = Multisite::getSiteId();
            Log::debug('Saving deployment id for job', [$this->site_id]);
        }

        $attributes = $this->serializedSleep();

        return $attributes;
    }

    public function __wakeup()
    {
        if (isset($this->site_id) && $this->site_id) {
            Log::debug('Restoring deployment id for job', [$this->site_id]);
            Multisite::setSiteByID($this->site_id);
        }

        $this->serializedWakeup();
    }
}
