<?php
/**
 * Ushahidi Config Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Config as ConfigEntity;
use Ushahidi\Core\Entity\ConfigRepository as ConfigRepositoryContract;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\UpdateRepository;
use Ushahidi\Core\Exception\NotFoundException;

use League\Event\ListenerInterface;
use Ushahidi\Core\Traits\Event;
use DB;
use Database;

class ConfigRepository implements
	ReadRepository,
	UpdateRepository,
	ConfigRepositoryContract
{

	// Use Event trait to trigger events
	use Event;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	// ReadRepository
	public function getEntity(array $data = null)
	{
        return new ConfigEntity($data);
	}

	// ReadRepository
	// ConfigRepository
	public function get($group)
	{
		$this->verifyGroup($group);

		$query = DB::select('config.*')
			->from('config')
			->where('group_name', '=', $group)
			->execute($this->db);

		if (count($query))
		{
			$config = $query->as_array('config_key', 'config_value');
			$config = array_map(function ($config_value) {
				return json_decode($value, true);
			}, $config);
		}

		return $this->getEntity(['id' => $group] + $config);
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$intercom_data = [];
		$group = $entity->getId();

		$this->verifyGroup($group);

		// Intercom count datasources
		if ($group === 'data-provider') {
			$intercom_data['num_data_sources'] = 0;
			foreach ($entity->providers as $key => $value) {
				$value ? $intercom_data['num_data_sources']++ : null;
			}
		}

		$immutable = $entity->getImmutable();
		foreach ($entity->getChanged() as $key => $val) {
			// Emit Intercom Update events
			if ($key === 'description') {
				$intercom_data['has_description'] = true;
			}

			if ($key === 'image_header') {
				$intercom_data['has_logo'] = true;
			}

			// New User - set their deployment created date
			if ($key === 'first_login') {
				$intercom_data['deployment_created_date'] = date("Y-m-d H:i:s");
			}

			if (! in_array($key, $immutable)) {
				// Below is to reset the twitter-since_id when the search-terms are updated.
				// This should be revised when the data-source tech-debt is addressed

				if ($group = 'data-provider' &&
					$key === 'twitter' &&
					isset($config['twitter']) &&
					$val['twitter_search_terms'] !== $config['twitter']['twitter_search_terms']
				) {
					$this->insertOrUpdate('twitter', 'since_id', 0);
				}

				$this->insertOrUpdate($group, $key, $val);
			}
		}

		if ($intercom_data) {
			$user = service('session.user');
			$this->emit($this->event, $user->email, $intercom_data);
		}
	}

	private function insertOrUpdate($group, $key, $value)
	{
		$value = json_encode($value);

		try {
			DB::insert('config', array('group_name', 'config_key', 'config_value'))
				->values(array($group, $key, $value))
				->execute($this->db);
		} catch (\Database_Exception $e) {
			DB::update('config')
				->set(array('config_value' => $value))
				->where('group_name', '=', $group)
				->where('config_key', '=', $key)
				->execute($this->db);
		}
	}

	// ConfigRepository
	public function groups()
	{
		return [
			'features',
			'site',
			'test',
			'data-provider',
			'map',
			'twitter'
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
	protected function verifyGroups(array $groups)
	{
		$invalid = array_diff(array_values($groups), $this->groups());
		if ($invalid) {
			throw new NotFoundException(
				'Requested groups do not exist: ' . implode(', ', $invalid)
			);
		}
	}

	// ConfigRepository
	public function all(array $groups = null)
	{
		if ($groups) {
			$this->verifyGroups($groups);
		} else {
			$groups = $this->groups();
		}

		$result = array();
		foreach ($groups as $group) {
			$result[] = $this->get($group);
		}

		return $result;
	}
}
