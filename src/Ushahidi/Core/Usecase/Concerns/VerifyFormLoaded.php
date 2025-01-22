<?php

/**
 * Ushahidi Platform Verify Form Exists for Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Concerns;

use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Contracts\Repository\Entity\FormRepository;
use Ushahidi\Contracts\Repository\Entity\FormContactRepository;

trait VerifyFormLoaded
{
    /**
     * @var FormRepository
     */
    protected $form_repo;

    /**
     * @var FormContactRepository
     */
    protected $form_contact_repo;

    /**
     * @param  FormRepository $repo
     * @return void
     */
    public function setFormRepository(FormRepository $repo)
    {
        $this->form_repo = $repo;
    }

    /**
     * @param  FormRepository $repo
     * @return void
     */
    public function setFormContactRepository(FormContactRepository $repo)
    {
        $this->form_contact_repo = $repo;
    }

    /**
     * Checks that the form exists.
     *
     * @return void
     */
    protected function verifyFormExists()
    {
        // Ensure that the form exists.
        $form = $this->form_repo->get($this->getRequiredIdentifier('form_id'));
        $this->verifyEntityLoaded($form, $this->identifiers);
    }

    /**
     * Checks that the form exists.
     * @param  Data $input
     * @return void
     * @todo  maybe move to validator
     */
    protected function verifyFormDoesNoExistInTargetedSurveyState()
    {
        // Ensure that the form exists.
        if ($this->form_contact_repo->formExistsInPostStateRepo($this->getRequiredIdentifier('form_id'))) {
            // @todo Define a more sensible exception
            throw new ValidatorException('The form already has a set of contacts', [
                'id' => 'The form already has a set of contacts'
            ]);
        }
    }

    /**
     * Checks that the form exists.
     * @param  Data $input
     * @return void
     * @todo  maybe move to validator
     */
    protected function verifyTargetedSurvey()
    {
        $form = $this->form_repo->get($this->getRequiredIdentifier('form_id'));
        // Ensure that the form exists.
        if (!$form->targeted_survey) {
            // @todo Define a more sensible exception
            throw new ValidatorException('The form is not a targeted survey', [
                'id' => 'The form is not a targeted survey'
            ]);
        }
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
