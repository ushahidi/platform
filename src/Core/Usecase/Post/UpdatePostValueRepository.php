<?php

/**
 * Ushahidi Platform Update Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

interface UpdatePostValueRepository
{
	/**
	 * Create new post value
	 * @param  Mixed   $value
	 * @param  Int     $form_attribute_id
	 * @param  Int     $post_id
	 */
	public function createValue($value, $form_attribute_id, $post_id);

	/**
	 * Update an existing post value
	 * @param  Int     $id
	 * @param  Mixed   $value
	 * @param  void
	 */
	public function updateValue($id, $value);


	/**
	 * Delete values that are not in the ids array
	 * @param  Integer $post_id
	 * @param  Array   $ids
	 */
	public function deleteNotIn($post_id, Array $ids);
}
