<?php

/**
 * Ushahidi Platform Tag Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Tag;

use Ushahidi\Data;

class TagData extends Data
{
	public $tag;
	public $slug; // auto-filled
	public $description;
	public $type;
	public $color;
	public $icon;
	public $priority;
	public $role;
}
