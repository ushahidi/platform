<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Permission Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Entity\PermissionRepository;

class Ushahidi_Repository_Permission extends Ushahidi_Repository implements
	PermissionRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'permissions';
	}

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		return new Permission($data);
	}

	public function getSearchFields()
	{
		return ['q', /* LIKE name */];
	}

	// SearchRepository
	public function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->q)
		{
			$query->where('name', 'LIKE', "%" .$search->q ."%");
		}

		return $query;
	}

	// UshahidiRepository
	public function exists($permission)
	{
		return (bool) $this->selectCount(['name' => $permission]);
	}

}
