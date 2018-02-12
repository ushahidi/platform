<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Export Job Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository;

class Ushahidi_Repository_Export_Job extends Ushahidi_Repository implements ExportJobRepository
{
	protected function getTable()
	{
		return 'export_job';
	}

	// Ushahidi_Repository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		foreach ([
			'entity_type',
		] as $fk)
		{
			if ($search->$fk)
			{
				$query->where("export_job.{$fk}_id", '=', $search->$fk);
			}
		}
	}

	public function getEntity(Array $data = null)
	{
		return new ExportJob($data);
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$state = [
			'created' => time(),
		];

		return parent::create($entity->setState($state));
	}

	// WebhookJobRepository
	public function getJobs($limit)
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
			'entity_type'
		];
	}
}
