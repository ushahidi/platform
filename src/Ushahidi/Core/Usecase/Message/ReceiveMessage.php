<?php

/**
 * Ushahidi Platform Receive Message Use Case
 *
 * - Takes a received SMS message
 * - finds/creates the associated contact
 * - Stores the raw message
 * - Creates a new un-typed post from the message
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Message;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Validator;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Concerns\DispatchesEvents;
use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Core\Entity\ContactRepository;

class ReceiveMessage extends CreateUsecase
{
    use DispatchesEvents;

    /**
     * @var \Ushahidi\Core\Entity\ContactRepository
     */
    protected $contactRepo;

    /**
     * @var \Ushahidi\Contracts\Validator
     */
    protected $contactValidator;

    /**
     * Inject a contact repository
     *
     */
    public function setContactRepository(ContactRepository $contactRepo)
    {
        $this->contactRepo = $contactRepo;
        return $this;
    }

    /**
     * Inject a contact validator
     *
     * @param  $repo Validator
     * @return $this
     */
    public function setContactValidator(Validator $contactValidator)
    {
        $this->contactValidator = $contactValidator;
        return $this;
    }

    // Usecase
    public function interact()
    {
        // Fetch and hydrate the message entity...
        $entity = $this->getEntity();

        /*
         * re: github.com/ushahidi/platform/issues/2111
         * Message reception is not something that happens under the usual
         * authentication / authorization conditions. Each data provider
         * is responsible for them and the platform authorizer doesn't
         * have any data for making this decision.
         * Ergo, commenting this out:
         *
         * ... verify that the message entity can be created by the current user
         */
        // $this->verifyReceiveAuth($entity);

        // ... verify that the message entity is in a valid state
        $this->verifyValid($entity);

        // Find or create contact based on >$this->getPayload('from')
        $contact = $this->getContactEntity();

        // ... verify the contact is valid
        $this->verifyValidContact($contact);

        // ... create contact if it doesn't exist
        $contact_id = $this->createContact($contact);
        $entity->setState(compact('contact_id'));
        $id = null;

        // ... persist the new message entity
        $id = $this->repo->create($entity);

        $entity->setState(compact('id'));

        $this->dispatch('message.receive', [
            'id' => $id,
            'entity' => $entity,
            // @todo pass these some other way
            'inbound_form_id' => $this->getPayload('inbound_form_id', false),
            'inbound_fields' => $this->getPayload('inbound_fields', [])
        ]);

        return $id;
    }

    /**
     * Get an empty entity, apply the payload.
     *
     * @return \Ushahidi\Contracts\Entity
     */
    protected function getEntity()
    {
        return $this->repo->getEntity()->setState(
            $this->payload + [
                'status' => Message::RECEIVED,
                'direction' => Message::INCOMING
            ]
        );
    }

    /**
     * Create contact record for message
     *
     * @return \Ushahidi\Contracts\Entity $contact
     */
    protected function getContactEntity()
    {
        // Is the sender of the message a registered contact?
        $contact = $this->contactRepo->getByContact($this->getPayload('from'), $this->getPayload('contact_type'));
        if (!$contact->getId()) {
            // this is the first time a message has been received by this number, so create contact
            $contact =  $this->contactRepo->getEntity()->setState([
                'contact' => $this->getPayload('from'),
                'type' => $this->getPayload('contact_type'),
                'data_source' => $this->getPayload('data_source'),
            ]);
        }
        return $contact;
    }

    /**
     * Create contact (if its new)
     *
     * @param  \Ushahidi\Contracts\Entity $contact
     * @return int
     */
    protected function createContact(Entity $contact)
    {
        // If contact already existed, just return id.
        if ($contact->getId()) {
            return $contact->getId();
        }

        return $this->contactRepo->create($contact);
    }

    protected function verifyValidContact(Entity $contact)
    {
        if (!$this->contactValidator) {
            return;
        }

        // validate contact
        if (!$this->contactValidator->check($contact->asArray())) {
            $this->contactValidatorError($contact);
        }
    }

    /**
     * Throw a ValidatorException
     *
     * @param  \Ushahidi\Contracts\Entity $entity
     * @throws \Ushahidi\Core\Exception\ValidatorException
     */
    protected function contactValidatorError(Entity $entity)
    {
        throw new ValidatorException(
            sprintf(
                'Failed to validate %s entity',
                $entity->getResource()
            ),
            $this->contactValidator->errors()
        );
    }

    /**
     * Verifies the current user is allowed receive access on $entity
     *
     * @param \Ushahidi\Contracts\Entity $entity
     * @throws \Ushahidi\Core\Exception\AuthorizerException
     */
    protected function verifyReceiveAuth(Entity $entity)
    {
        $this->verifyAuth($entity, 'receive');
    }
}
