<?php

/**
 * Ushahidi Tag Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Tag;
use Ushahidi\Core\Entity\TagRepository;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\ParentAccess;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;
use Ushahidi\Core\Tool\Permissions\AclTrait;

// The `TagAuthorizer` class is responsible for access checks on `Tags`
class TagAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// - `AdminAccess` to check if the user has admin access
	use AdminAccess;

	// Adds isAllowedParent() method
	use ParentAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	// It uses `PrivateDeployment` to check whether a deployment is private
	use PrivateDeployment;

	// Check that the user has the necessary permissions
	// if roles are available for this deployment.
	use AclTrait;

	// It requires a `TagRepository` to load parents too.
	protected $tag_repo;

	/**
	 * @param TagRepository $tag_repo
	 */
	public function __construct(TagRepository $tag_repo)
	{
		$this->tag_repo = $tag_repo;
	}

	/* ParentAccess */
	protected function getParent(Entity $entity)
	{
		// If the post has a parent_id, we attempt to load it from the `PostRepository`
		if ($entity->parent_id) {
			return $this->tag_repo->get($entity->parent_id);
		}

		return false;
	}

	protected function isUserOfRole(Tag $entity, $user)
	{
		if ($entity->role) {
			return in_array($user->role, $entity->role);
		}

		// If no roles are selected, the Tag is considered completely public.
		return true;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		// Only logged in users have access if the deployment is private
		if (!$this->canAccessDeployment($user)) {
			return false;
		}

		// First check whether there is a role with the right permissions
		if ($this->acl->hasPermission($user, Permission::MANAGE_SETTINGS)) {
			return true;
		}

		// Then we check if a user has the 'admin' role. If they do they're
		// allowed access to everything (all entities and all privileges)
		if ($this->isUserAdmin($user)) {
			return true;
		}

		// We check if the user has access to a parent tag. This doesn't
		// grant them access, but is used to deny access even if the child tag
		// is public.
		if (! $this->isAllowedParent($entity, $privilege, $user)) {
			return false;
		}

		// Finally, we check if the Tag is only visible to specific roles.
		if ($privilege === 'read' && $this->isUserOfRole($entity, $user)) {
			return true;
		}

		if ($privilege === 'search') {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}
}
