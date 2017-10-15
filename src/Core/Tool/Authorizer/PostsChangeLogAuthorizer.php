<?php

/**
 * Ushahidi Post Changelog Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostRepository;  // ?
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\Permissions\AclTrait;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\OwnerAccess;
use Ushahidi\Core\Traits\PrivAccess;
use Ushahidi\Core\Traits\PrivateDeployment;

// The `PostsChangeLogAuthorizer` class is responsible for access checks on `Post Changelog`
class PostsChangeLogAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It uses methods from several traits to check access:
	// - `OwnerAccess` to check if a user owns the post, the
	// - `AdminAccess` to check if the user has admin access
	use AdminAccess, OwnerAccess;

	// Check that the user has the necessary permissions
	// if roles are available for this deployment.
	use AclTrait;
	// It requires a `PostRepository` to load the owning post.
	protected $post_repo;

	// It requires a `PostAuthorizer` to check privileges against the owning post.
	protected $post_auth;

	/**
	 * @param PostRepository $post_repo
	 */
	public function __construct(PostRepository $post_repo, PostAuthorizer $post_auth)
	{
		$this->post_repo = $post_repo;
		$this->post_auth = $post_auth;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		//$post = $this->getPost($entity);
		$user = $this->getUser();

		// First check whether there is a role with the right permissions
		if ($this->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
				return true;
		}

		// Then we check if a user has the 'admin' role. If they do they're
		// allowed access to everything (all entities and all privileges)
		if ($this->isUserAdmin($user)) {
				return true;
		}

		//default: denial
		return false;
	}

	/* Authorizer */
	public function getAllowedPrivs(Entity $entity)
	{
		$post = $this->getPost($entity);

		// All access is based on the post itself, not the changelog.
		return $this->post_auth->getAllowedPrivs($post);
	}

	/**
	 * Get the post associated with this changelog.
	 * @param  Entity $entity
	 * @return Post
	 */
	protected function getPost(Entity $entity)
	{
		return $this->post_repo->get($entity->post_id);
	}
}
