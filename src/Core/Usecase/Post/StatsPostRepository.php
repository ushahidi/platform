<?php

/**
 * Ushahidi Platform Stats Post Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\SearchData;

interface StatsPostRepository
{

	/**
	 * Get grouped totals for stats
	 * @param  SearchData $search
	 * @return Array
	 */
	public function getGroupedTotals(SearchData $search)
}
