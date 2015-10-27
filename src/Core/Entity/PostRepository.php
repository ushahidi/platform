<?php

/**
 * Repository for Posts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;

interface PostRepository extends
	EntityGet
{
	/**
	 * @param  int $id
	 * @param  int $parent_id
	 * @param  string $type
	 * @return \Ushahidi\Core\Entity\Post
	 */
	public function getByIdAndParent($id, $parent_id, $type);

	/**
	 * @param  string $locale
	 * @param  int $parent_id
	 * @param  string $type
	 * @return \Ushahidi\Core\Entity\Post
	 */
	public function getByLocale($locale, $parent_id, $type);

	/**
	 * Get total number of published posts
	 * @return int
	 */
	public function getPublishedTotal();
}
