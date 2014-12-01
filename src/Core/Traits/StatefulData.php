<?php

/**
 * Ushahidi Platform Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait StatefulData
{
	// Uses DataTransformer to ensure type consistency. Prevents unexpected failures
	// when comparing, for example, a string with a number (`'5' === 5`).
	use DataTransformer;

	/**
	 * Tracks which properties have been changed, separately from internal object
	 * properties, organized by the unique object id.
	 *
	 * @var Array
	 */
	protected static $changed = [];

	/**
	 * Sets initial state by hydrating the object with values in the data.
	 *
	 * @param Array $data
	 */
	public function __construct(Array $data = null)
	{
		// Initialize change tracking.
		static::$changed[$this->getObjectId()] = [];

		if ($data) {
			// Define the initial state.
			$this->setState($data);

			// Reset any changes caused by hydration.
			static::$changed[$this->getObjectId()] = [];
		}
	}

	/**
	 * Direct access to object properties must not be allowed, to maintain state.
	 * Object values can be directly read using this magic method, but cannot be
	 * directly written with `__set`.
	 *
	 * NOTE: All object properties should have `protected` or `private` visibility!
	 *
	 * @param  String $key
	 * @return Mixed
	 */
	abstract public function __get($key);

	/**
	 * Direct access to object properties must not be allowed, to maintain state.
	 *
	 * @param  String $key
	 * @param  Mixed  $value
	 * @return void
	 * @throws RuntimeException
	 */
	public function __set($key, $value)
	{
		throw new \RuntimeException(
			'Direct modification to stateful objects is not allowed, ' .
			'use the setState() method to change properties'
		);
	}

	/**
	 * Direct access to object properties must not be allowed, to maintain state.
	 *
	 * @param  String $key
	 * @return Boolean
	 */
	abstract public function __isset($key);

	/**
	 * Clear out changes tracking when object is deleted.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		// Reset change tracking.
		unset(static::$changed[$this->getObjectId()]);
	}

	/**
	 * Get a unique identifer for this object.
	 *
	 * @return String
	 */
	final protected function getObjectId()
	{
		return spl_object_hash($this);
	}

	/**
	 * Change the internal state of the entity, updating values and tracking any
	 * changes that are made.
	 *
	 * @param  Array  $data
	 * @return $this
	 */
	public function setState(Array $data)
	{
		// Allow for data to be filled in by deriving from other values.
		foreach ($this->getDerived() as $key => $from) {
			if (!array_key_exists($key, $data) && array_key_exists($from, $data)) {
				$data[$key] = $data[$from];
			}
		}

		// To prevent polluting object properties, changes are tracked through a
		// static property, indexed by the object id. This ensures that all object
		// properties are associated directly with the entity, and that the change
		// tracker will never appear when running `get_object_vars` or similar.
		//
		// tl;dr: it keeps the object properties clean and separated from state tracking.
		$changed =& static::$changed[$this->getObjectId()];

		foreach ($this->transform($data) as $key => $value) {
			if ($this->$key !== $value) {
				// Update the value...
				$this->setStateValue($key, $value);
				// ... and track the change.
				$changed[$key] = $key;
			}
		}

		return $this;
	}

	/**
	 * Direct access to object properties must not be allowed, to maintain state.
	 *
	 * This method is an alternative to `__set`, because PHP does not allow magic
	 * methods to have `protected` visibility.
	 *
	 * @param  String $key
	 * @param  Mixed  $value
	 * @return void
	 */
	abstract protected function setStateValue($key, $value);

	/**
	 * Check if a property has been changed.
	 *
	 * @param  String $key
	 * @return Boolean
	 */
	public function hasChanged($key)
	{
		return !empty(static::$changed[$this->getObjectId()][$key]);
	}

	/**
	 * Get all values that have been changed since initial state was defined.
	 *
	 * @return Array
	 */
	public function getChanged()
	{
		return array_intersect_key($this->asArray(), static::$changed[$this->getObjectId()]);
	}

	/**
	 * Get the current entity state as an associative array.
	 *
	 * @return Array
	 */
	abstract public function asArray();

	/**
	 * Return the data that can be derived from other values.
	 *
	 * @return Array
	 */
	protected function getDerived()
	{
		return [];
	}
}
