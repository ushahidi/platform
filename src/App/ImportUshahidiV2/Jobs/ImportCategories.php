<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;
use Ushahidi\App\ImportUshahidiV2\ManifestSchemas\ImportParameters;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ImportCategories extends ImportUshahidiV2Job
{
    use Concerns\ConnectsToV2DB;

    const BATCH_SIZE = 50;

    protected $dbConfig;
    protected $mappingRepo;
    protected $extraParams;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $importId, array $dbConfig, ImportParameters $extraParams)
    {
        parent::__construct($importId);
        $this->dbConfig = $dbConfig;
        $this->extraParams = $extraParams;
    }

    /**
     * Process the extra parameters that may have been provided for the import job
     */
    protected function processExtraParams()
    {
        /* Gather configured mappings */
        $catMaps = $this->extraParams->getCategoryMappings();
        
        /* Resolve and create the mappings */
        $importMappings = (new Collection($catMaps))->map(function ($m) {
            if (!$m->from->id || !$m->to->id) {
                throw new Exception("Category mapping is missing id for from or to");
            }
            // Check IDs
            // TODO

            Log::debug("Saving configured mapping of category id {src} to tag id {dest}", [
                'src' => $m->from->id ,
                'dest' => $m->to->id
            ]);

            // Create mapping
            return new ImportUshahidiV2\ImportMapping([
                'import_id' => $this->importId,
                'source_type' => 'category',
                'source_id' => $m->from->id,
                'dest_type' => 'tags',
                'dest_id' => $m->to->id,
                'established_by' => 'import-config',
            ]);
        });

        /* Create mappings */
        $this->mappingRepo->createMany($importMappings);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ImportUshahidiV2\Contracts\ImportMappingRepository $mappingRepo,
        Entity\TagRepository $destRepo,
        ImportUshahidiV2\Mappers\CategoryTagMapper $mapper
    ) {
        // Set up importer
        $importer = new ImportUshahidiV2\Importer(
            'category',
            $mapper,
            $mappingRepo,
            $destRepo
        );

        $this->mappingRepo = $mappingRepo;

        // Process provided extra parameters
        $this->processExtraParams();

        // We have to import parents and children in 2 steps to ensure the parents are all created
        // before their children and parent_id can be mapped to the new parent
        $this->importParents($importer);
        $this->importChildren($importer);
    }

    protected function importParents($importer)
    {
        $batch = 0;
        // While there are data left
        while (true) {
            // Fetch categories
            $sourceCats = $this->getConnection()
                ->table('category')
                ->select('category.*')
                ->where('parent_id', '=', 0)
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('id', 'asc')
                ->get();

            // If there is no more data
            if ($sourceCats->isEmpty()) {
                // Break out of the loop
                break;
            }

            // Exclude from the list cat mappings that are already present
            // (i.e. because they have been configured)
            $sourceCats = $sourceCats->filter(function ($v2_cat) {
                return !($this->mappingRepo->hasMapping($this->importId, 'category', $v2_cat->id));
            });

            $created = $importer->run($this->getImport(), $sourceCats);

            // Add to count
            $batch++;
        }
    }

    protected function importChildren($importer)
    {
        $batch = 0;
        // While there are data left
        while (true) {
            // Fetch categories
            $sourceCats = $this->getConnection()
                ->table('category')
                ->select('category.*')
                ->where('parent_id', '<>', 0)
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('id', 'asc')
                ->get();

            // If there is no more data
            if ($sourceCats->isEmpty()) {
                // Break out of the loop
                break;
            }

            // Exclude from the list cat mappings that are already present
            // (i.e. because they have been configured)
            $sourceCats = $sourceCats->filter(function ($v2_cat) {
                return !($this->mappingRepo->hasMapping($this->importId, 'category', $v2_cat->id));
            });

            $created = $importer->run($this->getImport(), $sourceCats);

            $batch++;
        }
    }
}
