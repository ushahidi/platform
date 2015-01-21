<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Role Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Role;
use Ushahidi\Core\Entity\RoleRepository;

class Ushahidi_Repository_Role extends Ushahidi_Repository implements RoleRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'roles';
	}

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		return new Role($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['q', /* LIKE name */];
	}

	// RoleRepository
	public function doRolesExist(Array $roles = null)
	{
		if (!$roles)
		{
			// 0 === 0, all day every day
			return true;
		}

		$found = (int) $this->selectCount(['name' => $roles]);
		return count($roles) === $found;
	}

	public function doesRoleExist($role)
	{
		if (!$role)
		{
			return false;
		}

		$found = (int) $this->selectCount(['name' => $role]);

		return (bool) $found;
	}
}
