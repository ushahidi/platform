<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Config Repository, using Kohana::$config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\Config as ConfigEntity;
use Ushahidi\Entity\ConfigRepository;

class Ushahidi_Repository_Config implements ConfigRepository
{
	public function groups()
	{
		return array(
			'site',
			'test',
			'features',
			'data-provider'
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
			$config = Kohana::$config->load($group)->as_array();
			$result[] = new ConfigEntity($config, $group);
		}

		return $result;
	}

	public function get($group)
	{
		$this->verifyGroup($group);

		$config = Kohana::$config->load($group)->as_array();

		return new ConfigEntity($config, $group);
	}

	public function set($group, $key, $value)
	{
		$this->verifyGroup($group);

		if ($key === ConfigEntity::GROUP_KEY) {
			return $group;
		}

		$config = Kohana::$config->load($group);
		$config->set($key, $value);

		return $this->get($group, $key);
	}
}
