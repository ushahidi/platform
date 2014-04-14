<?php

/**
 * Ushahidi basic enitity class
 *
 * Entities should not have methods, they should only have properties!
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi;

abstract class Entity
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
