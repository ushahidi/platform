<?php

/**
 * Ushahidi Post Value Restrictions trait
 *
 *
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Entity\FormRepository;

trait PostValueRestrictions
{

	public function canUserSeePostLock(Post $post, $user)
	{
		// At present only logged in users with Manage Post Permission can see that a Post is locked
		return $this->canUserEditForm($post->form_id, $user);
	}

	public function canUserSeeAuthor(Post $post, FormRepository $form_repo, $user)
	{

		if ($post->form_id) {
			if ($this->canUserEditForm($post->form_id, $user)) {
				return true;
			}

			return !$form_repo->isAuthorHidden($post->form_id);
		}

		return true;
	}

	/**
	 * Test whether the post instance requires value restriction
	 * @param  Post $post
	 * @return Boolean
	 */
	public function canUserReadPostsValues(Post $post, $user)
	{
		return $this->canUserEditForm($post->form_id, $user);
	}

	/* FormRole */
	protected function canUserEditForm($form_id, $user)
	{
		return $this->isUserAdmin($user) || $this->acl->hasPermission($user, Permission::MANAGE_POSTS);
	}
}
