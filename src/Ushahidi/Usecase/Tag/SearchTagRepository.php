<?php

/**
 * Repository for Searching Tags
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Tag;

interface SearchTagRepository
{
	/**
	 * @param  Ushahidi\Usecase\Tag\SearchTagData $data
	 * @return [Ushahidi\Entity\Tag, ...]
	 */
	public function search(SearchTagData $data);
}
