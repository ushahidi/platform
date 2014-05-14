<?php

/**
 * Ushahidi Config Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;
use Ushahidi\Traits\ArrayExchange;

class Config extends Entity
{
	use ArrayExchange {
		// We modify the output of asArray, so we alias the implementation
		// to a private method for clarity.
		asArray as private asArraySimple;
	}

	// The @ symbol is not allowed in config keys. Conveniently, this makes it
	// possible to use for setting a consistent, non-conflicting property.
	const GROUP_KEY = '@group';

	public function __construct($data = null, $group = null)
	{
		$this->setGroup($group);

		if ($data) {
			$this->setData($data);
		}
	}

	/**
	 * @param string $group
	 * @return $this
	 */
	public function setGroup($group) {
		$this->{static::GROUP_KEY} = $group;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGroup() {
		return $this->{static::GROUP_KEY};
	}

	public function setData($data)
	{
		// Config is one of the few entities that does not have a static set
		// of variables. As such, it does not require checking if properties
		// exist before setting values.
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}
		return $this;
	}

	public function asArray()
	{
		// Plain hash of config does not include the group name
		$data = $this->asArraySimple();
		unset($data[static::GROUP_KEY]);
		return $data;
	}

	public function getResource()
	{
		return 'config';
	}
}
