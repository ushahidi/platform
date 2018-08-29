<?php

/**
 * Ushahidi Import Listener
 *
 * Listens for new imports
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Ushahidi\App\Repository\PostRepository;
use Ushahidi\Core\Entity\CSV;
use Ushahidi\Core\Entity\Set;

use Ushahidi\Core\Tool\Transformer;

use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Tool\ValidatorTrait;

class ImportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        ValidatorTrait;
    protected $transformer;
    protected $repo;
    protected $entities;
    protected $records;
    protected $csvImportJob;
    protected $importUsecase;

    public function verify($entity)
    {
        // ... verify that the entity can be created by the current user
        $this->verifyCreateAuth($entity);
        // ... verify that the entity is in a valid state
        $this->verifyValid($entity);
    }

    // ValidatorTrait
    protected function verifyValid(\Ushahidi\Core\Entity\Post $entity)
    {
        if (!$this->validator->check($entity->asArray())) {
            $this->validatorError($entity);
        }
    }
    /**
     * Inject a repository that can create entities.
     *
     * @param  $repo ImportRepository
     * @return $this
     */
    public function setRepository(PostRepository $repo)
    {
        $this->repo = $repo;
        return $this;
    }
    public function setUsecase($usecase)
    {
        $this->importUsecase = $usecase;
    }
    /**
     * @param Transformer $transformer
     * @return $this
     */
    public function setTransformer(Transformer $transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * Create a new job instance.
     *
     * @param  CSVImport  $csvImportJob
     * @param $records
     * @return void
     */
    public function __construct($importJob)
    {
        /**
         * auto_detect_line_endings: Support all line endings without manually specifying it
         * (primarily added because of OS9 line endings which do not work by default)
         */
        ini_set('auto_detect_line_endings', 1);
        ini_set('memory_limit', '-1');
        //set_time_limit(720);
        ini_set('max_execution_time', 720);
        $this->csvImportJob = $importJob;
    }


    /**
     * @param $record
     * @return mixed
     */
    protected function transform($record)
    {
        $record = $this->transformer->interact($record);

        return $this->repo->getEntity()->setState($record);
    }

    private function readCSV()
    {

        $fs = service('tool.filesystem');
        $reader = service('filereader.csv');
        $this->transformer = service('transformer.csv');
        $csv = $this->csvImportJob;
        // Read file
        $file = new \SplTempFileObject();
        $contents = $fs->read($csv->filename);
        $file->fwrite($contents);

        // Get records
        // @todo read up to a sensible offset and process the rest later
        $records = $reader->process($file);

        // Set map and fixed values for transformer
        $this->transformer->setMap($csv->maps_to);
        $this->transformer->setFixedValues($csv->fixed);
        return $records;
    }
    public function handle()
    {
        ini_set('auto_detect_line_endings', 1);
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 720);
        $records = $this->readCSV();
        $this->validator = service('factory.validator')->get('posts', 'import');
        $this->auth = service('authorizer.console');
        $this->repo = service('repository.post');
        $processed = $errors = 0;
        $collection_id = service('repository.set')->create(new Set([
            'name' => $this->csvImportJob->filename,
            'description' => 'Import',
            'view' => 'data',
            'featured' => false
        ]));
        foreach ($records as $index => $record) {
            // ... transform record
            $entity = $this->transform($record);

            // Ensure that under review is correctly mapped to draft
            if (strcasecmp($entity->status, 'under review')== 0) {
                $entity->setState(['status' => 'draft']);
            }

            if (!service('csv-speedup.enabled')) {
                $this->verify($entity);
            }
            // ... persist the new entity
            try {
                $id = $this->repo->create($entity);
            } catch (Exception $e) {
                $errors++;
            }
            service('repository.set')->addPostToSet($collection_id, $id);

            $processed++;
        }

        $new_status = 'SUCCESS';
        $this->csvImportJob->setState([
            'status' => $new_status,
            'collection_id' => $collection_id,
            'processed' => $processed,
            'errors' => $errors
        ]);

        service('repository.csv')->update($this->csvImportJob);
    }
}
