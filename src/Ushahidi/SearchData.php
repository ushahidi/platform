<?php

/**
 * Ushahidi Platform Search Data Interface
 *
 * Methods required for searching.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi;

use Ushahidi\Traits\ArrayExchange;

interface SearchData
{
	/**
	 * Get an array of the sorting parameters and their values.
	 * @return Array [orderby, order, limit, offset]
	 */
	public function getSortingParams();
}
