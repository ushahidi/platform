<?php

/**
 * Ushahidi Platform Entity Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Traversable;
use Ushahidi\Core\Entity\CSV;
use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Usecase;
use Ushahidi\Core\Concerns\Event;
use Ushahidi\Contracts\Transformer;
use Ushahidi\Core\Usecase\Concerns\Formatter as FormatterTrait;
use Ushahidi\Core\Usecase\Concerns\Validator as ValidatorTrait;
use Ushahidi\Core\Usecase\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\Core\Usecase\Concerns\Translator as TranslatorTrait;
use Ushahidi\Contracts\Repository\ImportRepository;

class ImportPost implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        TranslatorTrait,
        ValidatorTrait;

    // Use Event trait to trigger events
    use Event;

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

    /**
     * Inject a repository that can create entities.
     *
     * @todo  setPayload doesn't match signature for other usecases
     *
     * @param  \Iterator $payload
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
     * @param  \Iterator $repo
     * @return $this
     */
    public function setTransformer(Transformer $transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    protected $csv;

    public function setCSV(CSV $csv)
    {
        $this->csv = $csv;
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
        // Start count of records processed, and errors
        $processed = $errors = 0;

        // Fetch an empty entity..
        $entity = $this->getEntity();

        // ... verify that the entity can be created by the current user
        $this->verifyImportAuth($entity);

        $new_status = 'PENDING';
        $this->csv->setState([
            'status' => $new_status
        ]);

        service('repository.csv')->update($this->csv);
        $this->emit(
            $this->event,
            $this->payload,
            $this->csv,
            $this->transformer,
            $this->repo,
            $this
        );
        // ... and return the formatted entity
        return [
            'status' => $new_status
        ];
    }

    public function verify($entity)
    {
        // ... verify that the entity can be created by the current user
        $this->verifyCreateAuth($entity);

        // ... verify that the entity is in a valid state
        $this->verifyValid($entity);
    }

    // ValidatorTrait
    protected function verifyValid(Entity $entity)
    {
        if (!$this->validator->check($entity->asArray())) {
            $this->validatorError($entity);
        }
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
}
