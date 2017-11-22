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

	use RecursiveArrayDiff;

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
	public function __construct(array $data = null)
	{
		// Initialize change tracking.
		static::$changed[$this->getObjectId()] = [];

		$data = $data ?: [];

		// We can't define the method getDefaultData in this trait
		// due to the way method overriding works with trait inheritance.
		// The class using this trait can override the method,
		// but a subclass of that class cannot.
		if (method_exists($this, 'getDefaultData')) {
			// fill in available defaults for any missing values
			foreach ($this->getDefaultData() as $key => $default_value) {
				if (!isset($data[$key])) {
					$data[$key] = $default_value;
				}
			}
		}

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
	public function setState(array $data)
	{

		// Allow for data to be filled in by deriving from other values.
		foreach ($this->getDerived() as $key => $possible) {
			if (!array_key_exists($key, $data)) {
				if (!is_array($possible)) {
					// Always possible to derive data from more than one source.
					$possible = [$possible];
				}
				foreach ($possible as $from) {
					if (is_callable($from)) {
						// Callable function which returns the derived value
						//
						// function ($data) {
						//     return $data['foo'] . '-' . uniqid();
						// }
						//
						if ($derivedValue = $from($data)) {
							$data[$key] = $derivedValue;
						}
					} elseif (array_key_exists($from, $data)) {
						// Derived value comes from a simple alias:
						//
						//     $data['foo'] = $data['bar'];
						//
						$data[$key] = $data[$from];
					} elseif (strpos($from, '.')) {
						// Derived value comes from a complex alias:
						//
						//     $data['foo'] = $data['relation']['bar'];
						//
						list($arr, $from) = explode('.', $from, 2);
						if (array_key_exists($arr, $data)
							&& is_array($data[$arr])
							&& array_key_exists($from, $data[$arr])
						) {
							$data[$key] = $data[$arr][$from];
						}
					}
				}
			}
		}

		$this->collectChangesToEntity($data);

		return $this;
	}


	//TO ASK:
		// IF a value is sent that is now null or empty,
		//	should we assume that's correct? or should we NOT overwrite the
		//	existing value with blank data?

	// To prevent polluting object properties, changes are tracked through a
	// static property, indexed by the object id. This ensures that all object
	// properties are associated directly with the entity, and that the change
	// tracker will never appear when running `get_object_vars` or similar.
	//
	// tl;dr: it keeps the object properties clean and separated from state tracking.
	protected function collectChangesToEntity(array $new_data)
	{
		$immutable = $this->getImmutable();

		//Recursive helper function
		//NOTE: maybe not backwards compatible
		$recursiveCompareOldNewValues = function($key, $existing_val, $new_val) use (&$recursiveCompareOldNewValues){

			if(is_array($existing_val) && is_array($new_val))
			{
				//then cycle through each of those values
				foreach ($existing_val as $subkey => $subvalue) //use current obj as source of truth
				{
					if(array_key_exists($subkey, $new_val))
					{
						$recursiveCompareOldNewValues($subkey, $subvalue, $new_val[$subkey]);
					}else{
						$this->addKeyToChangedWithOldAndNewValues($subkey, $existing_val, $new_val);
					}
				}
				foreach ($new_val as $subkey => $subvalue) //use current obj as source of truth
				{
					if(array_key_exists($subkey, $existing_val))
					{
						//TODO: pass along an array of parent keys, so we know where to save this in the changed array
						$recursiveCompareOldNewValues($subkey, $existing_val[$subkey], $subvalue);
					}else{
					 	$this->addKeyToChangedWithOldAndNewValues($subkey, $existing_val, $new_val);
					}
				}
			}
			else if( is_array($existing_val) && !is_array($new_val)) // Why does this happen?
			{
				//if new array is empty, then just ignore it?
				// i.e., should new blank data always overwrite existing data?
				//	or should it mean this new data should be ignored?

				$this->addKeyToChangedWithOldAndNewValues($key, $existing_val, $new_val);
				$this->setStateValue($key, $new_val);

			}else if( !is_array($existing_val) && is_array($new_val))
			{
				$this->addKeyToChangedWithOldAndNewValues($key, $existing_val, $new_val);
				$this->setStateValue($key, $new_val);
			}
			//base case -- neither values are arrays -- we've arrived at a 'leaf' in our recursion
			else if(!is_array($existing_val) && !is_array($new_val))
			{
				//first, we first check items for objects that can't be compared with a simple '=='

				//check for datetime objects and compare them
				if ($existing_val instanceof \DateTime && $new_val instanceof \DateTime) {
					$timediff = abs( $existing_val->getTimestamp() - $new_val->getTimestamp());
					if ($timediff > 0) { // if they're not the same..
						$this->addKeyToChangedWithOldAndNewValues($key, $existing_val, $new_val);
						$this->setStateValue($key, $new_val);
					}
				//otherwise, assume we're just comparing simple values
				}else if($existing_val !== $new_val){
						$this->addKeyToChangedWithOldAndNewValues($key, $existing_val, $new_val);
						$this->setStateValue($key, $new_val);
				}
			}
		};

		//now cycle through each of the attributes of the new_data
		foreach ($this->transform($new_data) as $key => $value) {

			if (in_array($key, $immutable) && $this->$key) {
				// Value has already been set and cannot be changed.
				continue;
			}
			//otherwise, let's compare
			$recursiveCompareOldNewValues($key, $this->$key, $value);
		} //end for
	}

	//TODO: what if we collide with a value actually called - e.g., 'old_value'?
	protected function addKeyToChangedWithOldAndNewValues($key, $oldValue, $newValue)
	{
		static::$changed[$this->getObjectId()][$key] = array('old_value' => $oldValue, 'new_value'=>$newValue);
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
	 * @param  String $array_key the sub key we want to check, presently we
	 *         only go one level deep within nested arrays
	 * @return Boolean
	 */
	public function hasChanged($key, $array_key = null)
	{
		// Check if key exists in changed array
		$result = !empty(static::$changed[$this->getObjectId()][$key]);

		// If value to be checked is an array
		if ($result) {
			if (is_array(static::$changed[$this->getObjectId()][$key]) && $array_key) {
				return in_array($array_key, static::$changed[$this->getObjectId()][$key]);
			}
		}
		return $result;
	}

	public function getAllChangedFor($key)
	{
	    $result = !empty(static::$changed[$this->getObjectId()][$key]);
			return static::$changed[$this->getObjectId()][$key];
	}

	/**
	 * Get all values that have been changed since initial state was defined.
	 *
	 * @return Array
	 */

	//TODO: this might be broken now
	public function getChanged()
	{
		// Array comparison
		return array_intersect_key($this->asArray(), static::$changed[$this->getObjectId()]);
	}

	//TODO: maybe this shouldn't exist
	public function getChangedArray()
	{
		return static::$changed[$this->getObjectId()];
	}

	//TODO: this might be broken now
	public function getNewChangedValueForKey($key)
	{
		return static::$changed[$this->getObjectId()][$key]['new_value'];
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

	/**
	 * Return the names of values that cannot be modified once set.
	 *
	 * @return Array
	 */
	protected function getImmutable()
	{
		return [];
	}
}
