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

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Traits\VerifyEntityLoaded;

use Ushahidi\Core\Exception\NotFoundException;

trait VerifyFormLoaded
{
	// Traits in trait inception, giving access to the verifyEntityLoaded method.
	use VerifyEntityLoaded;

	/**
	 * @var Ushahidi\Core\Entity\FormRepository
	 */
	protected $form_repo;

	/**
	 * @param  Ushahidi\Core\Entity\FormRepository $repo
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
	protected function verifyFormExists(Data $input)
	{
		$form = $this->form_repo->get($input->form_id);
		// Tie the form repository and the entity loaded trait together.
		$this->verifyEntityLoaded($form, $input->form_id);
	}

	// Usecase
	public function interact(Data $input)
	{
		// Before running the rest of the use case, verify that the form exists.
		$this->verifyFormExists($input);
		return parent::interact($input);
	}
}
