<?php

/**
 * Ushahidi Array Exchange Trait
 *
 * Gives objects two new methods:
 *
 * 1. `setData($arrayhash)`, updating the object properties and returning `$this`
 * 2. `asArray()`, getting the object properties as an array
 *
 * Also defines a default constructor that takes an array as the only argument
 * and passes it to `setData` if it is not empty.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

trait ArrayExchange
{
	/**
	 * @param  array  $data  initial value
	 * @return void
	 */
	public function __construct($data = null)
	{
		if ($data) {
			$this->setData($data);
		}
	}

	/**
	 * @param  array  $data  new values
	 * @return $this
	 */
	public function setData($data)
	{
		foreach ($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public function asArray()
	{
		return get_object_vars($this);
	}
}
