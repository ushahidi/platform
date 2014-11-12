<?php

/**
 * Ushahidi Platform Data Provider Search Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\DataProvider;

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Traits\Data\SortableData;

class SearchDataProviderData extends SearchData
{
	public $type;

	public function getSortingParams()
	{
		// No sorting is enabled.
		return [];
	}
}
