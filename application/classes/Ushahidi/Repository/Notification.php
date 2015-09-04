<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Notification Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Notification;

class Ushahidi_Repository_Notification extends Ushahidi_Repository
{
	protected function notificationExists(Entity $entity)
	{
		$result = DB::select('id')
			->from('notifications')
			->where('contact_id', '=', $entity->contact_id)
			->and_where('set_id', '=', $entity->set_id)
			->execute($this->db);
		return (bool) $result->get('id', 0);
	}

	protected function getTable()
	{
		return 'notifications';
	}

	// Ushahidi_Repository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		foreach ([
			'is_subscribed',
		] as $key)
		{
			if ($search->$key)
			{
				$query->where("notifications.{$key}", '=', $search->$key);
			}
		}

		foreach ([
			'contact',
			'set',
		] as $fk)
		{
			if ($search->$fk)
			{
				$query->where("notifications.{$fk}_id", '=', $search->$fk);
			}
		}
	}

	public function getEntity(Array $data = null)
	{
		return new Notification($data);
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		// Return a 204 since we won't be creating this subscription
		if ($this->notificationExists($entity)) {
			return;
		}

		$state = [
			'contact_id' => $entity->contact_id,
			'created' => time(),
			'is_subscribed' => 1,
		];

		return parent::create($entity->setState($state));
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$state = [
			'updated'  => time(),
		];

		return parent::update($entity->setState($state));
	}

	public function getSearchFields()
	{
		return [
			'contact',
			'set',
			'is_subscribed'
		];
	}
}
