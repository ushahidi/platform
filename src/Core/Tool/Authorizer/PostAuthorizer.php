<?php

/**
 * Ushahidi Post Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Permissions\Acl;
use Ushahidi\Core\Tool\Permissions\Permissionable;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\OwnerAccess;
use Ushahidi\Core\Traits\ParentAccess;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivateDeployment;
use Ushahidi\Core\Traits\PermissionAccess;
use Ushahidi\Core\Traits\Permissions\ManagePosts;

// The `PostAuthorizer` class is responsible for access checks on `Post` Entities
class PostAuthorizer implements Authorizer, Permissionable
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It uses methods from several traits to check access:
	// - `OwnerAccess` to check if a user owns the post, the
	// - `ParentAccess` to check if the user can access a parent post,
	// - `AdminAccess` to check if the user has admin access
	use AdminAccess, OwnerAccess, ParentAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	// It uses `PrivateDeployment` to check whether a deployment is private
	use PrivateDeployment;

	// Check that the user has the necessary permissions
	// if roles are available for this deployment.
	use PermissionAccess;

	// Provides `getPermission`
	use ManagePosts;

	/**
	 * Get a list of all possible privilges.
	 * By default, returns standard HTTP REST methods.
	 * @return Array
	 */
	protected function getAllPrivs()
	{
		return ['read', 'create', 'update', 'delete', 'search', 'change_status'];
	}

	// It requires a `PostRepository` to load parent posts too.
	protected $post_repo;

	/**
	 * @param UserRepository $user_repo
	 * @param PostRepository $post_repo
	 */
	public function __construct(PostRepository $post_repo)
	{
		$this->post_repo = $post_repo;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// Only logged in users have access if the deployment is private
		if (!$this->hasAccess()) {
			return false;
		}

		// First check whether there is a role with the right permissions
		if ($this->hasPermission($user)) {
			return true;
		}

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

		// Non-admin users are not allowed to create posts for other users.
		// Post must be created for owner, or if the user is anonymous post must have no owner.
		if ($privilege === 'create'
			&& !$this->isUserOwner($entity, $user)
			&& !$this->isUserAndOwnerAnonymous($entity, $user)
			) {
			return false;
		}

		// Non-admin users are not allowed to change post status
		if (in_array($privilege, ['create', 'update']) && $entity->hasChanged('status')) {
			return false;
		}

		// All users are allowed to create and search posts.
		if (in_array($privilege, ['create', 'search'])) {
			return true;
		}

		// If a post is published, then anyone with the appropriate role can read it
		if ($privilege === 'read' && $this->isPostPublishedToUser($entity, $user)) {
			return true;
		}

		// If entity isn't loaded (ie. pre-flight check) then *anyone* can view it.
		if ($privilege === 'read' && ! $entity->getId()) {
			return true;
		}

		// We check if the user is the owner of this post. If so, they are allowed
		// to do almost anything, **except** change ownership and status of the post, which
		// only admins can do.
		if ($this->isUserOwner($entity, $user) && !$entity->hasChanged('user_id')
			&& $privilege !== 'change_status') {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}

	protected function isPostPublishedToUser(Entity $entity, $user)
	{
		if ($entity->status === 'published' && $this->isUserOfRole($entity, $user)) {
			return true;
		}
		return false;
	}

	protected function isUserOfRole(Entity $entity, $user)
	{
		if ($entity->published_to) {
			return in_array($user->role, $entity->published_to);
		}

		// If no visibility info, assume public
		return true;
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
