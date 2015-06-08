<?php

/**
 * Ushahidi Platform Set Post Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Set;

interface SetPostRepository
{
	/**
	 * @param  Int    $post_id
	 * @param  Int    $set_id
	 * @return Post
	 */
	public function getPostInSet($post_id, $set_id);
}
