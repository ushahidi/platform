<?php

/**
 * Ushahidi Platform Create Form Attribute Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Core\Usecase\Contact\CreateContact;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;

class CreateFormContact extends CreateContact
{
    use VerifyFormLoaded;

    // For form check:
    use VerifyEntityLoaded;
    use IdentifyRecords;
    protected function getEntity()
    {
        $entity = parent::getEntity();

        // Add user id if this is not provided
        if (empty($entity->user_id) && $this->auth->getUserId()) {
            $entity->setState(['user_id' => $this->auth->getUserId()]);
        }

        return $entity;
    }

    // Usecase
    public function interact()
    {

        // First verify that the form even exists
        $this->verifyFormExists();
        $this->verifyTargetedSurvey();
        $this->verifyFormDoesNoExistInTargetedSurveyState();
        // Fetch a default entity and ...
        $entity = $this->getEntity();
        // ... verify the current user has have permissions
        $this->verifyCreateAuth($entity);
        /**
         * @TODO Add validation so that we throw a warning
         * to users if they add contacts that are already part of a targeted survey
        */

        $entities = [];
        $invalid = [];
        $contacts = explode(',', $this->getPayload('contacts'));
        foreach ($contacts as $contact) {
            $entities[] = $this->getContactEntity($contact, $invalid);
        }

        return $this->getContactCollection($entities, $invalid);
    }

    private function getContactEntity($contactNumber)
    {
        // .. generate an entity for the item
        $entity = [
            'created' => time(),
            'can_notify' => true,
            'type' => 'phone',
            'contact' => $contactNumber
        ];
        $entity = $this->repo->getEntityWithData($contactNumber, $entity);
        return $entity;
    }

    private function getContactCollection($entities, $invalid)
    {
        // FIXME: move to collection error trait?
        if (!empty($invalid)) {
            $invalidList = implode(',', array_keys($invalid));
            throw new ValidatorException(sprintf(
                'The following contacts have validation errors:',
                $invalidList
            ), $invalid);
        } else {
            // ... persist the new collection
            $invalidEntities = $this->repo->updateCollection($entities, intval($this->getIdentifier('form_id')));
            // ... and finally format it for output
            return $this->formatter->__invoke(intval($this->getIdentifier('form_id')), $entities, $invalidEntities);
        }
    }
}
