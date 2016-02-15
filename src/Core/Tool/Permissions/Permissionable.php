<?php

/**
 * Ushahidi Platform Permissionable interface
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Permissions;

interface Permissionable
{
	/**
	 * Get a list of required permissions
	 *
	 * @return Array
	 */
	public function getPermissions();
}
