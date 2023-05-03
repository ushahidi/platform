<?php

namespace Ushahidi\Core\Concerns;

use Ushahidi\Core\Facade\Site;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\SerializesModels;

trait SiteAware
{
    /**
     * @var int The hostname ID of the previously active deployment
     */
    protected $site;

    use SerializesModels {
        __sleep as serializedSleep;
        __wakeup as serializedWakeup;
    }

    public function __sleep()
    {
        if (!$this->site) {
            $this->site = Site::instance()->getId();
            Log::debug('Saving deployment id for job', [$this->site]);
        }

        $attributes = $this->serializedSleep();

        return $attributes;
    }

    public function __wakeup()
    {
        if (isset($this->site) && $this->site) {
            Log::debug('Restoring deployment id for job', [$this->site]);
            Event::dispatch('site.restored', ['site' => $this->site]);
        }

        $this->serializedWakeup();
    }
}
