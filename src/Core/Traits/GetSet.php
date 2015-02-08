<?php

/**
 * Ushahidi GetSet Trait
 *
 * Adds simple get() & set() storage to any class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait GetSet
{
    /**
     * Key-based storage for get/set calls
     * @var Array
     */
    protected $getset_storage = [];

    /**
     * Get a specific key from the storage array,
     * optionally provide a default or force an error if not found.
     *
     * @param  string  $key
     * @param  mixed   $default A default value to return if the key is not found
     * @param  boolean $strict  Whether an error should be thrown if no key is found
     * @return mixed
     * @throws \LogicException If $strict is true and no key is found
     */
    public function get($key, $default = null, $strict = false)
    {
        if (array_key_exists($key, $this->getset_storage)) {
            return $this->getset_storage[$key];
        }

        if ($strict) {
            throw new \LogicException("Must call set('$key') before calling get('$key')");
        }

        return $default;
    }

    /**
     * Store a value under a specific key in the object's storage array.
     *
     * Alternatively, pass a single array in the format of 'key' => 'value'
     * as the first parameter.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value = null)
    {
        $map = is_array($key) ? $key : [$key => $value];
        foreach ($map as $k => $v) {
            $this->getset_storage[$k] = $v;
        }
    }
}
