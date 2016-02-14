<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Acl Manager
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Permissions\Acl;
use Ushahidi\Core\Traits\PermissionAccess;

class Ushahidi_AclManager implements Acl
{
	use PermissionAccess;
}
