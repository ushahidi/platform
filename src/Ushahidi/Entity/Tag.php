<?php

/**
 * Ushahidi Tag
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class Tag extends Entity
{
	public $id;
	public $parent_id;
	public $tag;
	public $slug;
	public $type;
	public $color;
	public $icon;
	public $description;
	public $priority;
	public $created;
	public $role;

	public function getResource()
	{
		return 'tags';
	}
}
