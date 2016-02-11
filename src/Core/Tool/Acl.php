<?php

/**
 * Ushahidi Platform Acl interface
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface Acl
{
	/**
	 * Check if Role has permission
	 *
	 * @param String $role
	 * @param Array $permissions A list of permissions to check for
	 * @return Boolean
	 */
	public function hasPermission($role, Array $permissions);

	/**
	 * Get a list of required permissions
	 *
	 * @return Array
	 */
	public function getRequiredPermissions();
}
