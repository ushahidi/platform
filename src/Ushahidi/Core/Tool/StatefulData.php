<?php

/**
 * Ushahidi Platform Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Ushahidi\Core\Concerns\DeriveData;
use Ushahidi\Core\Concerns\DefaultData;
use Ushahidi\Core\Concerns\TransformData;
use Ushahidi\Core\Concerns\RecursiveArrayDiff;

trait StatefulData
{
    use DefaultData;

    use DeriveData;

    // Uses DataTransformer to ensure type consistency. Prevents unexpected failures
    // when comparing, for example, a string with a number (`'5' === 5`).
    use TransformData;

    use RecursiveArrayDiff;

    /**
     * Tracks which properties have been changed, separately from internal object
     * properties, organized by the unique object id.
     *
     * @var array
     */
    protected static $changed = [];

    /**
     * Sets initial state by hydrating the object with values in the data.
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        // Initialize change tracking.
        static::$changed[$this->getObjectId()] = [];

        $data = $this->addDefaultDataToArray($data);

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
     * @param  string $key
     * @return mixed
     */
    abstract public function __get($key);

    /**
     * Direct access to object properties must not be allowed, to maintain state.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     * @throws \RuntimeException
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
     * @param  string $key
     * @return boolean
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
     * @return string
     */
    final protected function getObjectId()
    {
        return spl_object_hash($this);
    }

    /**
     * Change the internal state of the entity, updating values and tracking any
     * changes that are made.
     *
     * @param  array  $data
     * @return $this
     */
    public function setState(array $data)
    {
        // Allow for data to be filled in by deriving from other values.
        $data = $this->derive($data);

        // To prevent polluting object properties, changes are tracked through a
        // static property, indexed by the object id. This ensures that all object
        // properties are associated directly with the entity, and that the change
        // tracker will never appear when running `get_object_vars` or similar.
        //
        // tl;dr: it keeps the object properties clean and separated from state tracking.
        $changed =& static::$changed[$this->getObjectId()];

        // Get the immutable values. Once set, these cannot be changed.
        $immutable = $this->getImmutable();

        foreach ($this->transform($data) as $key => $value) {
            if (in_array($key, $immutable) && $this->$key) {
                // Value has already been set and cannot be changed.
                continue;
            }

            if (is_array($value)) {
                $current_key = is_array($this->$key) ? $this->$key : [$this->$key];

                // Check for multi level recursion
                $diff = array_merge(
                    $this->arrayRecursiveDiff($value, $current_key),
                    $this->arrayRecursiveDiff($current_key, $value)
                );
                // If arrays differ, *or* if this is the first time
                // we're setting this key
                if (!empty($diff) || !isset($this->$key)) {
                    // This is considered as a full update
                    // in the Repository update the array field will be overwritten
                    // with the new data
                    $this->setStateValue($key, $value);
                    // Track changes for changed array keys
                    $changed[$key] = array_keys($diff);
                }
            // Compare DateTime Objects
            } elseif ($value instanceof \DateTimeInterface && $this->$key instanceof \DateTimeInterface) {
                $current_key = $this->$key;

                $stored_date_ts = $current_key->getTimestamp();
                $received_date_ts = $value->getTimestamp();
                $timestamp_diff = abs($stored_date_ts - $received_date_ts);

                // TODO: should we set a tolerance for how much variation is allowed in milliseconds?
                if ($timestamp_diff > 0) {
                    // Update the value...
                    $this->setStateValue($key, $value);
                    // ... and track the change.
                    $changed[$key] = $key;
                }
            } elseif ($this->$key !== $value) {
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
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    abstract protected function setStateValue($key, $value);

    /**
     * Check if a property has been changed.
     *
     * @param  string $key
     * @param  string $array_key the sub key we want to check, presently we
     *         only go one level deep within nested arrays
     * @return boolean
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

    /**
     * Get all values that have been changed since initial state was defined.
     *
     * @return array
     */
    public function getChanged()
    {
        // Array comparison
        return array_intersect_key($this->asArray(), static::$changed[$this->getObjectId()]);
    }

    /**
     * Get the current entity state as an associative array.
     *
     * @return array
     */
    abstract public function asArray();

    /**
     * Return the data that can be derived from other values.
     *
     * @return array
     */
    protected function getDerived()
    {
        return [];
    }

    /**
     * Return the names of values that cannot be modified once set.
     *
     * @return array
     */
    protected function getImmutable()
    {
        return [];
    }
}
