<?php

/**
 * Repository for Posts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface PostRepository
{

	/**
	 * @param  int $id
	 * @return \Ushahidi\Entity\Post
	 */
	public function get($id);

	/**
	 * @param $parent \Ushahidi\Entity\Post
	 * @return [\Ushahidi\Entity\Post, ...]
	 */
	public function getAllByParent(Post $parent);

	/**
	 * @param $user \Ushahidi\Entity\User
	 * @return [\Ushahidi\Entity\Post, ...]
	 */
	public function getAllByUser(User $user);

	/**
	 * @param \Ushahidi\Entity\Post
	 * @return boolean
	 */
	public function add(Post $role);

	/**
	 * @param \Ushahidi\Entity\Post
	 * @return boolean
	 */
	public function remove(Post $role);

	/**
	 * @param \Ushahidi\Entity\Post
	 * @return boolean
	 */
	public function edit(Post $role);

}

