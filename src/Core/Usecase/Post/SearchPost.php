<?php

/**
 * Ushahidi Platform Entity Search Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Usecase\SearchUsecase;

class SearchPost extends SearchUsecase
{

	/**
	 * Get filter parameters that are used for paging.
	 *
	 * @return Array
	 */
	protected function getPagingFields()
	{
		return [
			'orderby' => 'created',
			'order'   => 'asc',
			'limit'   => null,
			'offset'  => 0
		];
	}
}
