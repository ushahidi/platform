<?php

/**
 * Ushahidi Platform Dynamic Entity
 *
 * Dynamic entities have unknown properties and can be mutated to any structure.
 * Object properties are faked through an internal storage array.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core;

use Ushahidi\Core\Traits\StatefulData;

abstract class DynamicEntity implements Entity
{
	// Dynamic entities are stateful.
	use StatefulData;

	/**
	 * @var Array
	 */
	protected $data = [];

	/**
	 * Transparent access to dynamic entity properties.
	 *
	 * @param  String $key
	 * @return Mixed
	 */
	public function __get($key)
	{
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	/**
	 * Transparent checking of dynamic entity properties.
	 *
	 * @param  String $key
	 * @return Boolean
	 */
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	// StatefulData
	protected function setStateValue($key, $value)
	{
		$this->data[$key] = $value;
	}

	// Entity
	public function asArray()
	{
		return $this->data;
	}

	// Entity
	public function getId()
	{
		return $this->id;
	}

	// StatefulData
	protected function getImmutable()
	{
		return ['id', 'allowed_privileges'];
	}
}
