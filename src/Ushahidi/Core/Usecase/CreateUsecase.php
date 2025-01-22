<?php

/**
 * Ushahidi Platform Entity Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Usecase;
use Ushahidi\Core\Concerns\DispatchesEvents;
use Ushahidi\Core\Usecase\Concerns\ModifyRecords;
use Ushahidi\Contracts\Repository\CreateRepository;
use Ushahidi\Core\Usecase\Concerns\Formatter as FormatterTrait;
use Ushahidi\Core\Usecase\Concerns\Validator as ValidatorTrait;
use Ushahidi\Core\Usecase\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\Core\Usecase\Concerns\Translator as TranslatorTrait;

class CreateUsecase implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        TranslatorTrait,
        ValidatorTrait;

    // - ModifyRecords for setting entity modification parameters
    use ModifyRecords;

    // - Provides dispatch()
    use DispatchesEvents;

    /**
     * @var CreateRepository
     */
    protected $repo;

    /**
     * Inject a repository that can create entities.
     *
     * @param  $repo CreateRepository
     * @return $this
     */
    public function setRepository(CreateRepository $repo)
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
        // Fetch a default entity and apply the payload...
        $entity = $this->getEntity();

        // ... verify that the entity can be created by the current user
        $this->verifyCreateAuth($entity);

        // ... verify that the entity is in a valid state
        if ($this->validator) {
            $this->verifyValid($entity);
        }

        // ... persist the new entity
        $id = $this->repo->create($entity);

        // ... get the newly created entity
        $entity = $this->getCreatedEntity($id);

        // ... dispatch an event and let other services know
        $this->dispatch($entity->getResource(). '.create', [
            'id' => $id,
            'entity' => $entity,
        ]);

        // ... check that the entity can be read by the current user
        if ($this->auth->isAllowed($entity, 'read')) {
            // ... and either return the formatted entity
            return $this->formatter ? ($this->formatter)($entity) : $entity;
        } else {
            // ... or just return nothing
            return;
        }
    }

    // ValidatorTrait
    protected function verifyValid(Entity $entity)
    {
        if (!$this->validator->check($entity->asArray())) {
            $this->validatorError($entity);
        }
    }

    /**
     * Get an empty entity, apply the payload.
     *
     * @return Entity
     */
    protected function getEntity()
    {
        return $this->repo->getEntity($this->payload);
    }

    /**
     * Get the created entity.
     *
     * @param  Mixed $id
     * @return Entity
     */
    protected function getCreatedEntity($id)
    {
        return $this->repo->get($id);
    }
}
