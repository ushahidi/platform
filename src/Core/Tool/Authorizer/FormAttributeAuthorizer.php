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
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\UserContext;

// The `FormAttributeAuthorizer` class is responsible
// for access checks on Form Attributes
class FormAttributeAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// It requires a `FormStageRepository` to load the owning form.
	protected $stage_repo;

	// It requires a `FormStageAuthorizer` to check privileges against the owning form.
	protected $stage_auth;

	/**
	 * @param FormstageRepository $stage_repo
	 * @param FormstageAuthorizer $stage_auth
	 */
	public function __construct(FormStageRepository $stage_repo, FormStageAuthorizer $stage_auth)
	{
		$this->stage_repo = $stage_repo;
		$this->stage_auth = $stage_auth;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		$stage = $this->getFormStage($entity);

		// All access is based on the stage, not the attribute
		return $this->stage_auth->isAllowed($stage, $privilege);
	}

	/* Authorizer */
	public function getAllowedPrivs(Entity $entity)
	{
		$stage = $this->getFormStage($entity);

		// All access is based on the stage, not the attribute
		return $this->stage_auth->getAllowedPrivs($stage);
	}

	/**
	 * Get the form associated with this stage.
	 * @param  Entity $entity
	 * @return Form
	 */
	protected function getFormStage(Entity $entity)
	{
		return $this->stage_repo->get($entity->form_stage_id);
	}
}
