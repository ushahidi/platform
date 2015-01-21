<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Set Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Set;
use Ushahidi\Core\SearchData;

class Ushahidi_Repository_Set extends Ushahidi_Repository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'sets';
	}

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		return new Set($data);
	}

	// CreateRepository
	public function create(Entity $entity) {
		return parent::create($entity->setState(['created' => time()]));
	}

	// UpdateRepository
	public function update(Entity $entity) {
		return parent::update($entity->setState(['updated' => time()]));
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['user_id', 'q' /* LIKE name */];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$sets_query = $this->search_query;

		if ($search->q)
		{
			$sets_query->where('name', 'LIKE', "%{$search->q}%");
		}

		if ($search->user_id)
		{
			$sets_query->where('user_id', '=', $search->user_id);
		}
	}
}
