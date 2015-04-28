<?php

/**
 * Ushahidi Form Stage Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\UserContext;

// The `FormStageAuthorizer` class is responsible for access checks on `Forms`
class FormStageAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It requires a `FormRepository` to load the owning form.
	protected $form_repo;

	// It requires a `FormAuthorizer` to check privileges against the owning form.
	protected $form_auth;

	/**
	 * @param FormRepository $form_repo
	 */
	public function __construct(FormRepository $form_repo, FormAuthorizer $form_auth)
	{
		$this->form_repo = $form_repo;
		$this->form_auth = $form_auth;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		$form = $this->getForm($entity);

		// All access is based on the form itself, not the stage.
		return $this->form_auth->isAllowed($form, $privilege);
	}

	/* Authorizer */
	public function getAllowedPrivs(Entity $entity)
	{
		$form = $this->getForm($entity);

		// All access is based on the form itself, not the stage.
		return $this->form_auth->getAllowedPrivs($form);
	}

	/**
	 * Get the form associated with this stage.
	 * @param  Entity $entity
	 * @return Form
	 */
	protected function getForm(Entity $entity)
	{
		return $this->form_repo->get($entity->form_id);
	}
}
