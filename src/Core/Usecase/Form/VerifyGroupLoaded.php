<?php

/**
 * Ushahidi Platform Verify Form Group Exists for Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormGroupRepository;
use Ushahidi\Core\Traits\VerifyEntityLoaded;

use Ushahidi\Core\Exception\NotFoundException;

trait VerifyGroupLoaded
{
	/**
	 * @var FormRepository
	 */
	protected $form_group_repo;

	/**
	 * @param  FormGroupRepository $repo
	 * @return void
	 */
	public function setGroupRepository(FormGroupRepository $repo)
	{
		$this->form_group_repo = $repo;
	}

	/**
	 * Checks that the form exists.
	 * @param  Data $input
	 * @return void
	 */
	protected function verifyGroupExists(FormAttribute $entity)
	{
		// Ensure that the group exists.
		$group = $this->form_group_repo->get($entity->form_group_id);
		$this->verifyEntityLoaded($group, ['form_group_id' => $entity->form_group_id]);

		$expected_form_id = (int) $this->getRequiredIdentifier('form_id');

		if ($group->form_id !== $expected_form_id) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid form group used, group %d is not in form %d',
				$entity->form_group_id,
				$expected_form_id
			));
		}
	}

	// IdentifyRecords
	abstract protected function getRequiredIdentifier($name);

	// VerifyEntityLoaded
	abstract protected function verifyEntityLoaded(Entity $entity, $lookup);
}
