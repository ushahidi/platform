<?php

/**
 * Ushahidi Platform Tag Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Tag;

use Ushahidi\Core\Data;

class TagData extends Data
{
	public $id;
	public $tag;
	public $slug; // auto-filled
	public $description;
	public $type;
	public $color;
	public $icon;
	public $priority;
	public $role;

	// Data
	public function getDifferent(Array $compare)
	{
		// Overload the default getDifferent method because array_diff* does not
		// work with nested arrays. To work around it, convert roles to string
		// before comparison.
		$tagdata = $this->asArray();
		if (isset($tagdata['role']) && is_array($tagdata['role'])) {
			$tagdata['role'] = json_encode($tagdata['role']);
		}
		if (isset($compare['role']) && is_array($compare['role'])) {
			$compare['role'] = json_encode($compare['role']);
		}

		$delta = array_diff_assoc($tagdata, $compare);

		// And after comparison, we convert roles back again.
		if (isset($delta['role'])) {
			$delta['role'] = json_decode($delta['role']);
		}

		return new static($delta);
	}
}
