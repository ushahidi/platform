<?php

/**
 * Repository for Post Values
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface PostValueRepository
{

	/**
	 * @param  int $id
	 * @return Ushahidi\Entity\PostValue
	 */
	public function get($id);

	/**
	 * @param  int $post_id
	 * @return [Ushahidi\Entity\PostValue, ...]
	 */
	public function getAllForPost($post_id);

}
