<?php

/**
 * Repository for Tags
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface TagRepository
{
	/**
	 * @param  int $id
	 * @return Ushahidi\Entity\Tag
	 */
	public function get($id);

	/**
	 * @param  Ushahidi\Entity\TagSearchData $data
	 * @param  Array $params [limit, offset, orderby, order]
	 * @return [Ushahidi\Entity\Tag, ...]
	 */
	public function search(TagSearchData $data, Array $params = null);
}
