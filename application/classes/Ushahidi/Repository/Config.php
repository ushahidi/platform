<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Config Repository, using Kohana::$config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Config as ConfigEntity;
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\UpdateRepository;
use Ushahidi\Core\Exception\NotFoundException;

class Ushahidi_Repository_Config implements
	ReadRepository,
	UpdateRepository,
	ConfigRepository
{

	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new ConfigEntity($data);
	}

	// ReadRepository
	public function get($group)
	{
		$this->verifyGroup($group);

		$config = \Kohana::$config->load($group)->as_array();

		return new ConfigEntity(['id' => $group] + $config);
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$group = $entity->getId();

		$this->verifyGroup($group);

		$config = \Kohana::$config->load($group);

		foreach ($entity->getChanged() as $key => $val) {
			$config->set($key, $val);
		}
	}

	// ConfigRepository
	public function groups()
	{
		return [
			'site',
			'test',
			'features',
			'data-provider',
			'map'
		];
	}

	/**
	 * @param  string $group
	 * @throws InvalidArgumentException when group is invalid
	 * @return void
	 */
	protected function verifyGroup($group)
	{
		if ($group && !in_array($group, $this->groups())) {
			throw new NotFoundException('Requested group does not exist: ' . $group);
		}
	}

	/**
	 * @param  array $groups
	 * @throws InvalidArgumentException when any group is invalid
	 * @return void
	 */
	protected function verifyGroups(Array $groups)
	{
		$invalid = array_diff(array_values($groups), $this->groups());
		if ($invalid) {
			throw new NotFoundException(
				'Requested groups do not exist: ' . implode(', ', $invalid)
			);
		}
	}

	// ConfigRepository
	public function all(Array $groups = null)
	{
		if ($groups) {
			$this->verifyGroups($groups);
		} else {
			$groups = $this->groups();
		}

		$result = array();
		foreach ($groups as $group) {
			$config = \Kohana::$config->load($group)->as_array();
			$result[] = new ConfigEntity(['id' => $group] + $config);
		}

		return $result;
	}
}
