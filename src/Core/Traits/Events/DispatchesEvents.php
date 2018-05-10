<?php

/**
 * Add methods for dispatching events
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits\Events;

use Illuminate\Contracts\Events\Dispatcher;

trait DispatchesEvents
{
    protected $events;

    public function setDispatcher(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Trigger event
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    protected function dispatch($event, $payload = [], $halt = false)
    {
        $this->events->dispatch($event, $payload, $halt);
    }
}
