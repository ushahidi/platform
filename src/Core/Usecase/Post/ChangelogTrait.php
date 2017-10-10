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

trait ChangelogTrait
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
	 * @param $id The log id -- should return a single log record
	 * @return Entity
	 */
	protected function getLogEntity($id)
	{
		// ... attempt to load the entity
		$entity = $this->repo->get($id);

		// ... and verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, compact('id'));

		// ... then return it
		return $entity;
	}

	protected function getFullChangelog($post_id)
	{
		 ///TODO: how do we get ALL of the changelog records into an array?
		 $changelog_entity  = $this->repo->get($post_id);
		 $logarray = $this->repo->getPostChangelogs($post_id);

		 //TODO: FIX - Spoofing what the client expects...
		 $changelog_obj = new class{};
		 $changelog_obj->results = $logarray;
		 return $changelog_obj;
	}

	/**
	 * Find entity based on identifying parameters.
	 *
	 * @return Entity
	 */
	protected function getPostEntity()
	{
		$post_repo = $this->getPostRepository();
		// Entity will be loaded using the provided id
		$post_id = $this->getRequiredIdentifier('post_id');
		// ... attempt to load the entity
		$entity = $post_repo->get($post_id);
		// ... and verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, compact('id'));
		// ... then return it
		return $entity;
	}




}
