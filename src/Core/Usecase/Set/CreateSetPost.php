<?php

/**
 * Add post to Set Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Set;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\SetRepository;
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\VerifyEntityLoaded;
use Ushahidi\Core\Usecase\CreateUsecase;

class CreateSetPost extends CreateUsecase
{
	use IdentifyRecords,
		VerifyEntityLoaded,
		SetRepositoryTrait,
		GetSet,
		AuthorizeSet;

	// Usecase
	public function interact()
	{
		// First fetch the set entity
		$set = $this->getSetEntity();

		// ... and verify the set can be edited by the current user
		$this->verifySetUpdateAuth($set);

		// ... then verify we have a valid payload
		// @todo this is a bit of a hack to check we have an 'id' in the payload
		$this->verifyValidPayload($this->payload);

		// .. and fetchthe post...
		$post = $this->getEntity();

		// ... verify that the post is visible to the current user
		$this->verifyReadAuth($post);

		// if the post has not already been added, then
		if (!$this->setRepo->setPostExists($set->id, $post->id)) {
			// .. add the post to the set
			$this->setRepo->addPostToSet($set->id, $post->id);
		}

		// ... and return the formatted post
		return $this->formatter->__invoke($post);
	}

	/**
	 * Find entity based on identifying parameters.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		// Entity will be loaded using the provided id
		$id = $this->payload['id'];

		// ... attempt to load the entity
		$entity = $this->repo->get($id);

		// ... and verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, compact('id'));

		// ... then return it
		return $entity;
	}

	// @todo original verifyValid method only takes an Entity so renamed
	protected function verifyValidPayload($payload)
	{
		if (!$this->validator->check($payload)) {
			$this->validatorError($this->repo->getEntity());
		}
	}
}
