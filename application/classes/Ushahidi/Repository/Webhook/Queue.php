<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Webhook Queue Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\WebhookQueue;
use Ushahidi\Core\Entity\WebhookQueueRepository;

class Ushahidi_Repository_Webhook_Queue extends Ushahidi_Repository implements WebhookQueueRepository
{
	protected function getTable()
	{
		return 'webhook_queue';
	}

	// Ushahidi_Repository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		foreach ([
			'post',
			'webhook',
		] as $fk)
		{
			if ($search->$fk)
			{
				$query->where("webhook_queue.{$fk}_id", '=', $search->$fk);
			}
		}
	}

	public function getEntity(Array $data = null)
	{
		return new WebhookQueue($data);
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$state = [
			'created' => time(),
		];

		return parent::create($entity->setState($state));
	}

	// WebhookQueueRepository
	public function getWebhooks($limit)
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
			'webhook'
		];
	}
}
