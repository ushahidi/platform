<?php

/**
 * Ushahidi Set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity;

class Set extends Entity
{
	public $id;
	public $name;
	public $url;
	public $filter;
	public $user_id;
	public $created;
	public $updated;

	public function getResource()
	{
		return 'sets';
	}
}
