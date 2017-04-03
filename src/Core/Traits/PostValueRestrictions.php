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
use Ushahidi\Core\Entity\FormRepository;

trait PostValueRestrictions
{

	public function canUserSeeAuthor(Post $post, FormRepository $form_repo)
	{
		if ($post->form_id) {
			return !$form_repo->isAuthorHidden($post->form_id);
		}

		return true;
	}

	protected function isUserOfRole($roles, $user)
	{
		if ($roles) {
			return in_array($user->role, $roles);
		}

		// If no visibility info, assume public
		return true;
	}

	protected function isPostPublishedToUser(Post $post, $user)
	{
		// Anon users can not see restricted fields
		if (!$user->getId()) {
			return false;
		}

		if ($post->status === 'published' && $this->isUserOfRole($post->published_to, $user)) {
			return true;
		}
		return false;
	}

	/**
	 * Test whether the post instance requires value restriction
	 * @param  Post $post
	 * @return Boolean
	 */
	public function canUserReadPostsValues(Post $post, $user, FormRepository $form_repo)
	{
		if ($this->canUserEditForm($post->form_id, $user, $form_repo) && $this->isPostPublishedToUser($post, $user))
		{
			return true;
		}
		return false;
	}

	/* FormRole */
	protected function canUserEditForm($form_id, $user, $form_repo)
	{
		// If the $entity->form_id exists and the $form->everyone_can_create is False
		// we check to see if the Form & Role Join exists in the `FormRoleRepository`
		\Log::instance()->add(\Log::NOTICE, "form: " . print_r($form_id,true));
		if ($form_id) {

			$roles = $form_repo->getRolesThatCanCreatePosts($form_id);
			\Log::instance()->add(\Log::NOTICE, "User: " . print_r($user,true));
			\Log::instance()->add(\Log::NOTICE, "roles: " . print_r($roles,true));
			if ($roles['everyone_can_create'] > 0) {
				return true;
			}
			if (is_array($roles['roles'])) {
				return $this->isUserOfRole($roles['roles'], $user);
			}
		}

		return false;
	}
}
