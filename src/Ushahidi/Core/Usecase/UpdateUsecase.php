<?php

/**
 * Ushahidi Platform Update Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Usecase;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\Core\Usecase\Concerns\ModifyRecords;
use Ushahidi\Contracts\Repository\UpdateRepository;
use Ushahidi\Core\Usecase\Concerns\Formatter as FormatterTrait;
use Ushahidi\Core\Usecase\Concerns\Validator as ValidatorTrait;
use Ushahidi\Core\Usecase\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\Core\Usecase\Concerns\Translator as TranslatorTrait;

class UpdateUsecase implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        TranslatorTrait,
        ValidatorTrait;

    // - IdentifyRecords for setting entity lookup parameters
    // - ModifyRecords for setting entity modification parameters
    use IdentifyRecords,
        ModifyRecords;

    // - VerifyEntityLoaded for checking that an entity is found
    use Concerns\VerifyEntityLoaded;

    /**
     * @var UpdateRepository
     */
    protected $repo;

    /**
     * Inject a repository that can update entities.
     *
     * @param  UpdateRepository $repo
     * @return $this
     */
    public function setRepository(UpdateRepository $repo)
    {
        $this->repo = $repo;
        return $this;
    }

    // Usecase
    public function isWrite()
    {
        return true;
    }

    // Usecase
    public function isSearch()
    {
        return false;
    }

    // Usecase
    public function interact()
    {
        // Fetch the entity and apply the payload...
        $entity = $this->getEntity()->setState($this->payload);

        // ... verify that the entity can be updated by the current user
        $this->verifyUpdateAuth($entity);

        // ... verify that the entity is in a valid state
        if ($this->validator) {
            $this->verifyValid($entity);
        }

        // ... persist the changes
        $this->repo->update($entity);

        // ... send notifications for changes made
        $this->sendNotifications($entity);

        // ... check that the entity can be read by the current user
        if ($this->auth->isAllowed($entity, 'read')) {
            // ... and either load the updated entity from the storage layer
            $updated_entity = $this->getEntity();

            // ... and return the updated, formatted entity
            return $this->formatter->__invoke($updated_entity);
        } else {
            // ... or just return nothing
            return;
        }
    }

    // Notification
    protected function sendNotifications(Entity $entity)
    {
        // Logic to send notifications
    }

    // ValidatorTrait
    protected function verifyValid(Entity $entity)
    {
        if (!$this->validator->check($entity->getChanged(), $entity->asArray())) {
            $this->validatorError($entity);
        }
    }

    /**
     * Find entity based on identifying parameters.
     *
     * @return \Ushahidi\Contracts\Entity
     */
    protected function getEntity()
    {
        // Entity will be loaded using the provided id
        $id = $this->getRequiredIdentifier('id');

        // ... attempt to load the entity
        $entity = $this->repo->get($id);

        // ... and verify that the entity was actually loaded
        $this->verifyEntityLoaded($entity, compact('id'));

        // ... then return it
        return $entity;
    }
}
