<?php

/**
 * Entity storage for Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Storage;
 use Ushahidi\Repository;
 use Ushahidi\Entity;

class Config implements Repository\Config
{

	protected $backend;

	public function __construct($backend)
	{
		$this->backend = $backend;
	}

	public function groups()
	{
		return array(
			'site',
			'test',
			'features',
		);
	}

	/**
	 * @param  string $group
	 * @throws InvalidArgumentException when group is invalid
	 * @return void
	 */
	protected function verifyGroup($group)
	{
		if ($group && !in_array($group, $this->groups())) {
			throw new \InvalidArgumentException("Requested group does not exist");
		}
	}

	public function all($group = null)
	{
		$this->verifyGroup($group);

		$groups = $group ? array($group) : $this->groups();

		$result = array();
		foreach ($groups as $group) {
			$config = $this->backend->load($group);
			foreach ($config as $key => $value) {
				$result[] = new Entity\Config(compact('group', 'key', 'value'));
			}
		}
		
		return $result;
	}

	public function get($group, $key)
	{
		$this->verifyGroup($group);

		$config = $this->backend->load($group);
		$value = $config->get($key);

		return new Entity\Config(compact('group', 'key', 'value'));
	}

	public function set($group, $key, $value)
	{
		$this->verifyGroup($group);

		$config = $this->backend->load($group);
		$config->set($key, $value);

		return $this->get($group, $key);
	}

}
