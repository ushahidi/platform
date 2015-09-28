<?php

/**
 * Ushahidi event trait
 *
 * Makes objects eventable
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use League\Event\EmitterInterface;
use League\Event\ListenerInterface;

trait Event
{
	protected $emitter;
	protected $event;

	public function setEmitter(EmitterInterface $emitter)
	{
		$this->emitter = $emitter;
	}

	/**
	 * Trigger event
	 * @param  string $event event name
	 * @return string event
	 */
	protected function emit($event)
	{
		$args = [$event] + func_get_args();
		return call_user_func_array([$this->emitter, 'emit'], $args);
	}

	/**
	 * Trigger events in batches
	 * @param  array $events array of event names
	 * @return array events
	 */
	protected function emitBatch($events)
	{
		return $this->emitter->emitBatch($events);
	}

	/**
	 * Add Event listener
	 * @param  string   $event triggered event
	 * @param  Listener $listener
	 * @param  int      $priority
	 * @return object   $this
	 */
	protected function addListener($event, ListenerInterface $listener, $priority = EmitterInterface::P_NORMAL)
	{
		$this->emitter->addListener($event, $listener, $priority);

		return $this;
	}

	public function setEvent($event)
	{
		$this->event = $event;
	}

	public function setListener(ListenerInterface $listener)
	{
		if (! $this->event) {
			throw new \LogicException('Cannot add a listener without an event');
		}
		
		$this->addListener($this->event, $listener);
	}
}
