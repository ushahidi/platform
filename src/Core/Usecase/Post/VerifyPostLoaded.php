<?php

/**
 * Ushahidi Platform Verify Post Exists for Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\PostRepository;

trait VerifyPostLoaded
{
	/**
	 * @var PostRepository
	 */
	protected $post_repo;

	/**
	 * @param  PostRepository $repo
	 * @return void
	 */
	public function setPostRepository(PostRepository $repo)
	{
		$this->post_repo = $repo;
	}

	/**
	 * Checks that the post exists.
	 * @param  Data $input
	 * @return void
	 */
	protected function verifyPostExists()
	{
		// Ensure that the post exists.
		$post = $this->post_repo->get($this->getRequiredIdentifier('post_id'));
		$this->verifyEntityLoaded($post, $this->identifiers);
	}

	// Usecase
	public function interact()
	{
		$this->verifyPostExists();
		return parent::interact();
	}

	// IdentifyRecords
	abstract protected function getRequiredIdentifier($name);

	// VerifyEntityLoaded
	abstract protected function verifyEntityLoaded(Entity $entity, $lookup);
}
