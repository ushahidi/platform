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

	protected $form_repository;

	public function restrictAuthor(Post $post, FormRepository $form_repo)
	{
		if ($post->form_id) {
			return $form_repo->getHideAuthor($post->form_id);
		}

		return false;
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
	public function restrictPostValues(Post $post, $user, FormRepository $form_repo)
	{
		$this->form_repository = $form_repo;
		if (!$this->isFormRestricted($post->form_id, $user) && $this->isPostPublishedToUser($post, $user))
		{
			return false;
		}
		return true;
	}

	/* FormRole */
	protected function isFormRestricted($form_id, $user)
	{
		// If the $entity->form_id exists and the $form->everyone_can_create is False
		// we check to see if the Form & Role Join exists in the `FormRoleRepository`

		if ($form_id) {
			$roles = $this->form_repository->getRolesThatCanCreatePosts($form_id);

			if ($roles['everyone_can_create'] > 0) {
				return false;
			}

			if ($this->isUserOfRole($roles, $user)) {
				return false;
			}
		}

		return true;
	}
}
