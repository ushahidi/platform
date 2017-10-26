<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Data Export Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\PostDataExport;
use Ushahidi\Core\Entity\PostDataExportRepository;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\AdminAccess;

class Ushahidi_Repository_PostDataExport extends Ushahidi_Repository implements PostDataExportRepository
{
	use UserContext;
	use AdminAccess;

	protected function getId(Entity $entity)
	{
		$result = $this->selectQuery()
			->where('user_id', '=', $entity->user_id)
			->execute($this->db);
		return $result->get('id', 0);
	}

	protected function getTable()
	{
		return 'postdataexports';
	}

	// Ushahidi_Repository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		$user = $this->getUser();

		// Limit search to user's records unless they are admin
		// or if we get user=me as a search param
		if (! $this->isUserAdmin($user) || $search->user === 'me') {
			$search->user = $this->getUserId();
		}

		foreach ([
			'user'
		] as $fk)
		{
			if ($search->$fk)
			{
				$query->where("postdataexports.{$fk}_id", '=', $search->$fk);
			}
		}
	}

	public function getEntity(Array $data = null)
	{
		return new Webhook($data);
	}


	// CreateRepository
	public function create(Entity $entity)
	{
		$id = $this->getId($entity);

		if ($id) {
			// No need to insert a new record.
			// Instead return the id of the Webhook that exists
			return $id;
		}

		$state = [
			'user_id' => $entity->user_id,
			'created' => time(),
		];

		return parent::create($entity->setState($state));
	}

	public function getSearchFields()
	{
		return [
			'user'
		];
	}
}
