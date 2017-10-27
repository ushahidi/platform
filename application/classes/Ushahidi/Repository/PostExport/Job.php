<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Data Export Job Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\PostExportJob;
use Ushahidi\Core\Entity\PostExportJobRepository;

class Ushahidi_Repository_PostExport_Job extends Ushahidi_Repository implements PostExportJobRepository
{
	protected function getTable()
	{
		return 'post_export_jobs';
	}

	// Ushahidi_Repository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		foreach ([
			'post_exports',
		] as $fk)
		{
			if ($search->$fk)
			{
				$query->where("post_export_jobs.{$fk}_id", '=', $search->$fk);
			}
		}
	}

	public function getEntity(Array $data = null)
	{
		return new PostExportJob($data);
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$state = [
			'created' => time(),
		];

		return parent::create($entity->setState($state));
	}

	// PostExportJobRepository
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
			'post_exports'
		];
	}
}
