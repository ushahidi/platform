<?php

/**
 * Post Repository Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Entity\PostRepository;

trait PostLockTrait
{
	protected $post_repo;

	public function setPostRepository(PostRepository $post_repo)
	{
		$this->post_repo = $post_repo;
		return $this;
	}

	public function getPostRepository()
	{
		return $this->post_repo;
	}

	/**
	 * Find lock entity based on id.
	 * @param $id The Lock id
	 * @return Entity
	 */
	protected function getLockEntity($id)
	{

		// ... attempt to load the entity
		$entity = $this->repo->get($id);

		// ... and verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, compact('id'));

		// ... then return it
		return $entity;
	}

	/**
	 * Find entity based on identifying parameters.
	 *
	 * @return Entity
	 */
	protected function getPostEntity()
	{
		$post_repo = $this->getPostRepository();

		$entity = $post_repo->getEntity();

		if ($id = $this->getIdentifier('post_id')) {
			// ... attempt to load the entity
			$entity = $post_repo->get($id);
			// ... and verify that the entity was actually loaded
			$this->verifyEntityLoaded($entity, compact('id'));
		}
		// ... then return it
		return $entity;
	}
}
