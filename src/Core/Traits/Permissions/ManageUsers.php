<?php

/**
 * Ushahidi Permissions Trait
 *
 * Implements Permissionable::getPermissions()
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits\Permissions;

use Ushahidi\Core\Entity;

trait ManageUsers
{
	// Acl Interface
	public function getPermission()
	{
		return 'Manage Users';
	}
}
