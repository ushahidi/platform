<?php

/**
 * Ushahidi Role
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class Role extends Entity
{
	public $name;
	public $display_name;
	public $description;

	public function getResource()
	{
		return 'roles';
	}
}
