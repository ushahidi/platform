<?php

/**
 * Ushahidi Platform Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tool;

use Ushahidi\Entity;

interface Authorizer
{
	/**
	 * Check if access to entity is allowed
	 * @param  Entity  $entity     Entity to check access to
	 * @param  string  $privilege  Privilege type to check access for
	 * @param  boolean $user       User requesting access
	 * @return boolean
	 */
	public function isAllowed(Entity $entity, $privilege, $user = false);
}
