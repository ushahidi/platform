<?php

/**
 * Ushahidi Post Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tool\Authorizer;

use Ushahidi\Entity;
use Ushahidi\Entity\User;
use Ushahidi\Entity\UserRepository;
use Ushahidi\Entity\PostRepository;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Traits\AdminAccess;
use Ushahidi\Traits\EnsureUserEntity;
use Ushahidi\Traits\OwnerAccess;
use Ushahidi\Traits\ParentAccess;

// The `PostAuthorizer` class is responsible for access checks on `Post` Entities
class PostAuthorizer implements Authorizer
{
	// It uses the EnsureUserEntity trait to load users if needed
	use EnsureUserEntity;

	// It uses methods from several traits to check access:
	// - `OwnerAccess` to check if a user owns the post, the
	// - `ParentAccess` to check if the user can access a parent post,
	// - `AdminAccess` to check if the user has admin access
	use AdminAccess, OwnerAccess, ParentAccess;

	// It requires a `PostRepository` to load parent posts too.
	protected $post_repo;

	/**
	 * @param UserRepository $user_repo
	 * @param PostRepository $post_repo
	 */
	public function __construct(UserRepository $user_repo, PostRepository $post_repo)
	{
		$this->user_repo = $user_repo;
		$this->post_repo = $post_repo;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege, $user = null)
	{
		// First we check we've got a `User` Entity.
		$this->ensureUserIsEntity($user);

		// Then we check if a user has the 'admin' role. If they do they're
		// allowed access to everything (all entities and all privileges)
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// We check if the user has access to a parent post. This doesn't
		// grant them access, but is used to deny access even if the child post
		// is public.
		if (! $this->isAllowedParent($entity, $privilege, $user)) {
			return false;
		}

		// We check if a user is the owner of this post, if so they have full access.
		if ($this->isUserOwner($entity, $user)) {
			return true;
		}

		// If a post is public then *anyone* can view it.
		if ($privilege === 'get' && $this->isPostPublic($entity)) {
			return true;
		}

		// All users are allowed to create posts.
		if ($privilege === 'create') {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}

	/**
	 * Check if a post is public
	 * @param  Entity  $entity
	 * @return boolean
	 */
	protected function isPostPublic(Entity $entity)
	{
		// To checking if a post is public we just check the post status is 'published'
		if ($entity->status === 'published') {
			return true;
		}

		return false;
	}

	/* ParentAccess */
	protected function getParent(Entity $entity)
	{
		// If the post has a parent_id, we attempt to load it from the `PostRepository`
		if ($entity->parent_id) {
			return $this->post_repo->get($entity->parent_id);
		}

		return false;
	}
}
