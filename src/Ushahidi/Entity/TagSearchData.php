<?php

/**
 * Ushahidi Platform Tag Search Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Data;

class TagSearchData extends Data
{
	public $q; // LIKE tag
	public $tag;
	public $type;
	public $parent;
}
