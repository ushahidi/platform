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

use League\Event\ListenerInterface;
use Ushahidi\Core\Traits\Event;

class Ushahidi_Repository_Config implements
	ReadRepository,
	UpdateRepository,
	ConfigRepository
{

	// Use Event trait to trigger events
	use Event;

	// ReadRepository
	public function getEntity(Array $data = null)
	{
	  return new ConfigEntity($data);
	}

	// ReadRepository
	// ConfigRepository
	public function get($group)
	{
		$this->verifyGroup($group);

		$config = \Kohana::$config->load($group)->as_array();

		return new ConfigEntity(['id' => $group] + $config);
	}

	// UpdateRepository
	public function update(Entity $entity)
	{

		$intercom_data = [];
		$group = $entity->getId();

		$this->verifyGroup($group);

		$config = \Kohana::$config->load($group);

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

				/* Below is to reset the twitter-since_id when the search-terms are updated. This should be revised when the data-source tech-debt is addressed*/

				if($key === 'twitter' && isset($config['twitter']) && $val['twitter_search_terms'] !== $config['twitter']['twitter_search_terms'])
				{
					$twitter_config = \Kohana::$config->load('twitter');
					$twitter_config->set('since_id', 0);
				}

				$config->set($key, $val);
			}
		}

		if ($intercom_data) {
			$this->emit($this->event, $intercom_data);
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
