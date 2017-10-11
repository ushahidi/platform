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
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\UserContext;

// The `PostsChangeLogAuthorizer` class is responsible for access checks on `Post Changelog`
class PostsChangeLogAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

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
		$post = $this->getPost($entity);

		// All access is based on the post itself, not the changelog.
		return $this->post_auth->isAllowed($post, $privilege);
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
