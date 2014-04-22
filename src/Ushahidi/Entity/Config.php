<?php

/**
 * Ushahidi File
 *
 * Description
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class Config extends Entity
{
	// The @ symbol is not allowed in config keys. Conveniently, this makes it
	// possible to use for setting a consistent, non-conflicting property.
	const GROUP_KEY = '@group';

	public function __construct($data = null, $group = '')
	{
		$this->setGroup($group);

		parent::__construct($data);
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
		$data = parent::asArray();
		unset($data[static::GROUP_KEY]);
		return $data;
	}

}
