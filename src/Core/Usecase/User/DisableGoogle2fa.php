<?php

/**
 * Ushahidi Platform User Verify Google 2fa Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2016 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Traits\IdentifyRecords;
use Ushahidi\Core\Traits\ModifyRecords;
use Ushahidi\Core\Traits\VerifyEntityLoaded;

class DisableGoogle2fa implements Usecase
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		FormatterTrait;

	// - IdentifyRecords for setting entity lookup parameters
	// - ModifyRecords for setting entity modification parameters
	use IdentifyRecords,
		ModifyRecords;

  // - VerifyEntityLoaded for checking that an entity is found
	use VerifyEntityLoaded;

	/**
	 * @var ResetPasswordRepository
	 */
	protected $repo;

	/**
	 * Inject a repository
	 *
	 * @param  $repo ResetPasswordRepository
	 * @return $this
	 */
	public function setRepository(ResetPasswordRepository $repo)
	{
		$this->repo = $repo;
		return $this;
	}

	// Usecase
	public function isWrite()
	{
		return false;
	}

	// Usecase
	public function isSearch()
	{
		return false;
	}

	public function interact()
	{
		// Fetch user by email
		$entity = $this->getEntity();

		if ($entity->getId()) {
			$this->repo->disableGoogle2fa($entity);
		}
		return;
	}

  /**
	 * Find entity based on identifying parameters.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		// Entity will be loaded using the provided id
		$id = $this->getRequiredIdentifier('id');

		// ... attempt to load the entity
		$entity = $this->repo->get($id);

		// ... and verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, compact('id'));

		// ... then return it
		return $entity;
	}
}
