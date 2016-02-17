<?php

/**
 * Ushahidi PermissionAccess Trait
 *
 * Implements Acl::hasPermission()
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Tool\Permissions\Acl;

trait PermissionAccess
{
	protected $acl;
	protected $roles_enabled = false;

	public function setRolesEnabled($roles_enabled)
	{
		$this->roles_enabled = $roles_enabled;
	}

	public function setAcl(Acl $acl)
	{
		$this->acl = $acl;
	}

	/**
	 * Check if custom roles are enabled for this deployment
	 * @return boolean
	 */
	protected function hasRolesEnabled()
	{
		return (bool) $this->roles_enabled;
	}

	/**
	 * Check if the user has permission
	 * @return boolean
	 */
	protected function hasPermission($user)
	{
		// Don't check for permissions if we don't have the
		// roles feature enabled
		if (!$this->hasRolesEnabled()) {
			return false;
		}
		
		return $this->acl->hasPermission($user, $this->getPermission());
	}
}
