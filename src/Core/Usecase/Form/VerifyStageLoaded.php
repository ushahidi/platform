<?php

/**
 * Ushahidi Platform Verify Form Stage Exists for Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormStageRepository;

use Ushahidi\Core\Exception\NotFoundException;

trait VerifyStageLoaded
{
	/**
	 * @var FormRepository
	 */
	protected $form_stage_repo;

	/**
	 * @param  FormStageRepository $repo
	 * @return void
	 */
	public function setStageRepository(FormStageRepository $repo)
	{
		$this->form_stage_repo = $repo;
	}

	/**
	 * Checks that the form exists.
	 * @param  Data $input
	 * @return void
	 */
	protected function verifyStageExists(FormAttribute $entity)
	{
		// Ensure that the stage exists.
		$stage = $this->form_stage_repo->get($entity->form_stage_id);
		$this->verifyEntityLoaded($stage, ['form_stage_id' => $entity->form_stage_id]);

		$expected_form_id = (int) $this->getRequiredIdentifier('form_id');

		if ($stage->form_id !== $expected_form_id) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid form stage used, stage %d is not in form %d',
				$entity->form_stage_id,
				$expected_form_id
			));
		}
	}

	// IdentifyRecords
	abstract protected function getRequiredIdentifier($name);

	// VerifyEntityLoaded
	abstract protected function verifyEntityLoaded(Entity $entity, $lookup);
}
