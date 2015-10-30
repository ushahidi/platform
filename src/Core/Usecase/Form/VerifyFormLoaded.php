<?php

/**
 * Ushahidi Platform Verify Form Exists for Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormRepository;

trait VerifyFormLoaded
{
	/**
	 * @var FormRepository
	 */
	protected $form_repo;

	/**
	 * @param  FormRepository $repo
	 * @return void
	 */
	public function setFormRepository(FormRepository $repo)
	{
		$this->form_repo = $repo;
	}

	/**
	 * Checks that the form exists.
	 * @param  Data $input
	 * @return void
	 */
	protected function verifyFormExists()
	{
		// Ensure that the form exists.
		$form = $this->form_repo->get($this->getRequiredIdentifier('form_id'));
		$this->verifyEntityLoaded($form, $this->identifiers);
	}

	// Usecase
	public function interact()
	{
		$this->verifyFormExists();
		return parent::interact();
	}

	// IdentifyRecords
	abstract protected function getRequiredIdentifier($name);

	// VerifyEntityLoaded
	abstract protected function verifyEntityLoaded(Entity $entity, $lookup);
}
