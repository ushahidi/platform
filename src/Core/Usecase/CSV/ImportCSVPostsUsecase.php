<?php

/**
 * Ushahidi Platform Entity Import CSV Posts
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
use Ushahidi\Core\Entity\Set;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Usecase;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi\Core\Tool\Transformer;
use Ushahidi\Core\Traits\Events\DispatchesEvents;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\SetRepository;
use Ushahidi\Core\Entity\CSVRepository;
use League\Flysystem\Filesystem;
use Ushahidi\Core\Tool\FileReader;
use Ushahidi\Core\Usecase\Concerns\VerifyEntityLoaded;
use Ushahidi\Core\Usecase\Concerns\IdentifyRecords;
use Ushahidi\App\Facades\Features;
use Ushahidi\Core\Traits\UserContext;

class ImportCSVPostsUsecase implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        ValidatorTrait;

    use IdentifyRecords;

    // - VerifyEntityLoaded for checking that an entity is found
    use VerifyEntityLoaded;

    // - Provides dispatch()
    use DispatchesEvents;

    use UserContext;

    public function __construct(
        PostRepository $postRepo,
        Filesystem $fs,
        FileReader $reader,
        Transformer $transformer,
        SetRepository $setRepo,
        CSVRepository $csvRepo,
        Authorizer $authorizer,
        Validator $validator
    ) {
        $this->fs = $fs;
        $this->reader = $reader;
        $this->transformer = $transformer;
        $this->setRepo = $setRepo;
        $this->csvRepo = $csvRepo;
        $this->postRepo = $postRepo;

        $this->setValidator($validator);
        $this->setAuthorizer($authorizer);
    }

    /**
     * @var ImportRepository
     */
    protected $postRepo;

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
        $processed = $errors = 0;

        $csv = $this->getCSV();

        // load the user from the job into the 'session'
        // the CSV itself doesn't save the creator so we're passing this
        // in from the job queue
        $this->session->setUser($this->getRequiredIdentifier('user_id'));

        // Read file
        $file = new \SplTempFileObject();
        $contents = $this->fs->read($csv->filename);
        $file->fwrite($contents);

        // Get records
        // @todo read up to a sensible offset and process the rest later
        $records = $this->reader->process($file);

        // Set map and fixed values for transformer
        $this->transformer->setMap($csv->maps_to);
        $this->transformer->setFixedValues($csv->fixed);

        $collection_id = $this->setRepo->create(new Set([
           'name' => $csv->filename,
           'description' => 'Import',
           'view' => 'data',
           'featured' => false,
           'user_id' => $this->getUserId()
        ]));

        $created_entities = [];
        foreach ($records as $index => $record) {
            // ... transform record
            $entity = $this->transform($record);

            // Ensure that under review is correctly mapped to draft
            if (strcasecmp($entity->status, 'under review')== 0) {
                $entity->setState(['status' => 'draft']);
            }

            if (!Features::isEnabled('csv-speedup')) {
                $this->verify($entity);
            }

            // ... persist the new entity
            try {
                $id = $this->postRepo->create($entity);
            } catch (Exception $e) {
                $errors++;
            }
            $this->setRepo->addPostToSet($collection_id, $id);

            $processed++;
        }

        $csv->setState([
            'status' => CSV::STATUS_SUCCESS,
            'collection_id' => $collection_id,
            'processed' => $processed,
            'errors' => $errors
        ]);

        $this->csvRepo->update($csv);

        return [
            'processed' => $processed,
            'errors' => $errors
        ];
    }

    /**
     * [transform description]
     * @return [type] [description]
     */
    protected function transform(array $record) : Post
    {
        $record = $this->transformer->interact($record);

        return $this->postRepo->getEntity()->setState($record);
    }

    public function verify(Post $entity)
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
     * Find entity based on identifying parameters.
     *
     * @return Entity
     */
    protected function getCSV() : CSV
    {
        // Entity will be loaded using the provided id
        $id = $this->getRequiredIdentifier('id');

        // ... attempt to load the entity
        $csv = $this->csvRepo->get($id);

        // ... and verify that the entity was actually loaded
        $this->verifyEntityLoaded($csv, compact('id'));

        // ... then return it
        return $csv;
    }
}
