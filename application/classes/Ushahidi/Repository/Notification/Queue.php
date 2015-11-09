<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Notification Queue Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\NotificationQueue;
use Ushahidi\Core\Entity\NotificationQueueRepository;

class Ushahidi_Repository_Notification_Queue extends Ushahidi_Repository implements NotificationQueueRepository
{
	protected function getTable()
	{
		return 'notification_queue';
	}

	// Ushahidi_Repository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;
				
		foreach ([
			'post',
			'set',
		] as $fk)
		{
			if ($search->$fk)
			{
				$query->where("notification_queue.{$fk}_id", '=', $search->$fk);
			}
		}
	}

	public function getEntity(Array $data = null)
	{
		return new NotificationQueue($data);
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$state = [
			'created' => time(),
		];

		return parent::create($entity->setState($state));
	}

	// NotificationQueueRepository
	public function getNotifications($limit)
	{
		$query = $this->selectQuery()
					  ->limit($limit)
					  ->order_by('created', 'ASC');

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	public function getSearchFields()
	{
		return [
			'post',
			'set'
		];
	}
}
