<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Acl
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Permissions\Acl;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\RoleRepository;

class Ushahidi_Acl implements Acl
{
	protected $role_repo;

	public function setRoleRepo(RoleRepository $role_repo)
	{
		$this->role_repo = $role_repo;
	}

	// Acl interface
	public function hasPermission(User $user, $permission)
	{
		if (!$user->role) {
			return false;
		}
		
		$role = $this->role_repo->getByName($user->role);

		// Does the user have the permission?
		return in_array($permission, $role->permissions);
	}
}
