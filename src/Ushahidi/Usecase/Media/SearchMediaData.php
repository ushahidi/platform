<?php

/**
 * Ushahidi Platform Media Search Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

use Ushahidi\SearchData;
use Ushahidi\Traits\Data\SortableData;

class SearchMediaData extends SearchData
{
	use SortableData;

	public $user;
	public $orphans;
}
