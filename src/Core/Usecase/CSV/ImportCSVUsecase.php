<?php

/**
 * Ushahidi Platform Entity Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\CSV;

use Traversable;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\CSV;
use Ushahidi\Core\Usecase;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi\Core\Tool\Transformer;
use Ushahidi\Core\Traits\Events\DispatchesEvents;
use Ushahidi\Core\Usecase\ImportRepository;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;

class ImportCSVUsecase implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait;

    use IdentifyRecords;

    // - VerifyEntityLoaded for checking that an entity is found
    use VerifyEntityLoaded;

    // - Provides dispatch()
    use DispatchesEvents;

    /**
     * @var ImportRepository
     */
    protected $repo;

    /**
     * Inject a repository that can create entities.
     *
     * @param  $repo ImportRepository
     * @return $this
     */
    public function setRepository(ImportRepository $repo)
    {
        $this->repo = $repo;
        return $this;
    }

    /**
     * @var Traversable
     */
    protected $payload;

    // Usecase
    public function isWrite()
    {
        return false;
    }

    // Usecase
    public function isSearch()
    {
        return false;
    }

    // Usecase
    public function interact()
    {
        // Fetch the csv..
        $entity = $this->getEntity();

        // ... verify that the entity can be created by the current user
        $this->verifyImportAuth($entity);

        // ... update the csv state
        $entity->setState([
            'status' => CSV::STATUS_PENDING
        ]);

        // ... persist the changes
        $this->repo->update($entity);

        // ... dispatch an event and let other services know
        $this->dispatch($entity->getResource(). '.import', [
            'id' => $entity->getId(),
            'entity' => $entity
        ]);

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

    /**
     * Find entity based on identifying parameters.
     *
     * @return Entity
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
