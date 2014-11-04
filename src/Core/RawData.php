<?php

/**
 * Ushahidi Platform Data Input for Use Cases
 *
 * Works very similar to "struct" objects in other languages (eg Ruby),
 * but requires that all possible properties are pre-defined, to ensure
 * a highly predictable object state.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core;

use Ushahidi\Core\Traits\ArrayExchange;

abstract class RawData extends Data
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
			// Unlike Data/ArrayExchange, RawData will allow any value to be set
			$this->$key = $value;
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
