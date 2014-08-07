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
	 * Check if access to an entity is allowed
	 * @param  Entity  $entity     Entity being accessed
	 * @param  String  $privilege  Privilege that is requested
	 * @param  Integer $user       User id requesting access
	 * @return Boolean
	 */
	public function isAllowed(Entity $entity, $privilege, $user = null);
}
