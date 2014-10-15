<?php

/**
 * Ushahidi Platform Update Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Post;

use Ushahidi\Data;
use Ushahidi\Entity;
use Ushahidi\Tool\AuthorizerTrait;
use Ushahidi\Tool\ValidatorTrait;
use Ushahidi\Traits\VerifyEntityLoaded;
use Ushahidi\Exception\ValidatorException;
use Ushahidi\Exception\AuthorizerException;

// @todo implement Usecase interface once D387 lands
class UpdatePost
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		ValidatorTrait;

	// - VerifyEntityLoaded for checking that an entity is found
	use VerifyEntityLoaded;

	// - FindPostEntity for loading the entity based on Read Data
	// This replaces the default getEntity() logic to allow loading
	// posts by locale, parent id and id.
	use FindPostEntity;

	// Ushahidi\Usecase\CreateRepository
	protected $repo;

	private $updated = [];

	public function __construct(Array $tools)
	{
		$this->setValidator($tools['valid']);
		$this->setAuthorizer($tools['auth']);
		$this->setRepository($tools['repo']);
	}

	private function setRepository(UpdatePostRepository $repo)
	{
		$this->repo = $repo;
	}

	public function interact(ReadPostData $read, UpdatePostData $input)
	{
		$entity = $this->getEntity($read);

		$this->verifyEntityLoaded($entity, $read->id);

		// We only want to work with values that have been changed
		$update = $input->getDifferent($entity->asArray());

		// Include type for use in validation and to stop the be updated
		// @todo figure out a better way to include these
		$update->type = $entity->type;
		$update->parent_id = $entity->parent_id;

		// Access checks
		$this->verifyUpdateAuth($entity, $update);

		if (!$this->valid->check($update)) {
			throw new ValidatorException('Failed to validate data for update', $this->valid->errors());
		}

		// Determine what changes to were made
		$this->updated = $update->asArray();

		$this->repo->update($entity->id, $update);

		// Reload the entity to get the most recent data
		$entity = $this->repo->get($entity->id);

		$this->verifyReadAuth($entity);

		return $entity;
	}

	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * Verifies the current user is allowed update access on $entity
	 *
	 * @param  Entity  $entity
	 * @param  Data    $input
	 * @return void
	 * @throws AuthorizerException
	 */
	private function verifyUpdateAuth(Entity $entity, Data $input)
	{
		$this->verifyAuth($entity, 'update');

		// if changing user id or author info
		if (isset($input->user_id) || isset($input->author_email) || isset($input->author_realname)) {
			$this->verifyAuth($entity, 'change_user');
		}
	}
}
