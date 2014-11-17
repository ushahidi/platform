<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Set Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Set;

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

	public function create(Data $data) {
		$record = array_filter($data->asArray());
		$record['created'] = time();

		return $this->executeInsert($record);
	}

	public function update($id, Data $data) {
		$record = array_filter($data->asArray());
		$record['updated'] = time();

		return $this->executeUpdate(compact('id'), $record);
	}
	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$sets_query = $this->search_query;

		$q = $search->q;
		if (! empty($q))
		{
			$sets_query->where('name', 'LIKE', "%$q%");
		}
		
		$set = $search->name;
		if (! empty($set))
		{
			$sets_query->where('name', '=', $set);
		}
		
		$user = $search->user;
		if(! empty($user))
		{
			$sets_query->where('user_id', '=', $user);
		}
	}
}
