<?php

/**
 * Entity storage for Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Storage\Kohana;

use \Kohana_Config as Backend;

use \Ushahidi\Entity\Config as ConfigEntity;
use \Ushahidi\Entity\ConfigRepository as ConfigRepositoryInterface;

class ConfigRepository implements ConfigRepositoryInterface
{

	protected $backend;

	public function __construct(Backend $backend)
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
			throw new \InvalidArgumentException("Requested group does not exist: " . $group);
		}
	}

	/**
	 * @param  array $groups
	 * @throws InvalidArgumentException when any group is invalid
	 * @return void
	 */
	protected function verifyGroups(array $groups)
	{
		$invalid = array_diff(array_values($groups), $this->groups());
		if ($invalid) {
			throw new \InvalidArgumentException(
				"Requested groups do not exist: " . implode(', ', $invalid)
			);
		}
	}

	public function all(array $groups = null)
	{
		if ($groups) {
			$this->verifyGroups($groups);
		} else {
			$groups = $this->groups();
		}

		$result = array();
		foreach ($groups as $group) {
			$config = $this->backend->load($group)->as_array();
			$result[] = new ConfigEntity($config, $group);
		}

		return $result;
	}

	public function get($group)
	{
		$this->verifyGroup($group);

		$config = $this->backend->load($group)->as_array();

		return new ConfigEntity($config, $group);
	}

	public function set($group, $key, $value)
	{
		$this->verifyGroup($group);

		$config = $this->backend->load($group);
		$config->set($key, $value);

		return $this->get($group, $key);
	}
}
