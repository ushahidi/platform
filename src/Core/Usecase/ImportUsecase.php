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

use Traversable;
use Ushahidi\App\Jobs\ImportJob;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi\Core\Tool\Transformer;
use Ushahidi\Core\Entity\CSV;

class ImportUsecase implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        ValidatorTrait;
    /**
     * @var ImportRepository
     */
    protected $repo;
    protected $csv;

    public function setCSV(CSV $csv)
    {
        $this->csv = $csv;
        return $this;
    }

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

    /**
     * Inject a repository that can create entities.
     *
     * @todo  setPayload doesn't match signature for other usecases
     *
     * @param  $repo Iterator
     * @return $this
     */
    public function setPayload(Traversable $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @var Transformer
     */
    protected $transformer;

    /**
     * Inject a repository that can create entities.
     *
     * @param  $repo Iterator
     * @return $this
     */
    public function setTransformer(Transformer $transformer)
    {
        $this->transformer = $transformer;
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
        // Fetch an empty entity..
        $entity = $this->getEntity();
        // ... verify that the entity can be created by the current user
        $this->verifyImportAuth($entity);
        $new_status = 'PENDING';
        $this->csv->setState([
            'status' => $new_status
        ]);
        service('repository.csv')->update($this->csv);
        dispatch(new ImportJob($this->csv));
        // ... and return the formatted entity
        return [
            'status' => $new_status
        ];
    }

    /**
     * Get an empty entity
     *
     * @return Entity
     */
    protected function getEntity()
    {
        return $this->repo->getEntity();
    }

    /**
     * Verify that the given entity is valid.
     *
     * @param  Entity $entity
     * @return void
     */
    protected function verifyValid(Entity $entity)
    {
        // TODO: Implement verifyValid() method.
    }
}
