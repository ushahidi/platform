<?php

/**
 * Ushahidi Form Attribute Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormGroupRepository;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\UserContext;

// The `FormAttributeAuthorizer` class is responsible
// for access checks on Form Attributes
class FormAttributeAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It requires a `FormGroupRepository` to load the owning form.
	protected $group_repo;

	// It requires a `FormGroupAuthorizer` to check privileges against the owning form.
	protected $group_auth;

	/**
	 * @param FormGroupRepository $group_repo
	 * @param FormGroupAuthorizer $group_auth
	 */
	public function __construct(FormGroupRepository $group_repo, FormGroupAuthorizer $group_auth)
	{
		$this->group_repo = $group_repo;
		$this->group_auth = $group_auth;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		$group = $this->getFormGroup($entity);

		// All access is based on the group, not the attribute
		return $this->group_auth->isAllowed($group, $privilege);
	}

	/* Authorizer */
	public function getAllowedPrivs(Entity $entity)
	{
		$group = $this->getFormGroup($entity);

		// All access is based on the group, not the attribute
		return $this->group_auth->getAllowedPrivs($group);
	}

	/**
	 * Get the form associated with this group.
	 * @param  Entity $entity
	 * @return Form
	 */
	protected function getFormGroup(Entity $entity)
	{
		return $this->group_repo->get($entity->form_group_id);
	}
}

