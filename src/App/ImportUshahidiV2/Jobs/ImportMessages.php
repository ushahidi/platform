<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\DB;
use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;

class ImportMessages extends ImportUshahidiV2Job
{
    use Concerns\ConnectsToV2DB;

    const BATCH_SIZE = 1000;

    protected $dbConfig;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $importId, array $dbConfig)
    {
        parent::__construct($importId);
        $this->dbConfig = $dbConfig;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ImportUshahidiV2\Contracts\ImportMappingRepository $mappingRepo,
        Entity\MessageRepository $destRepo,
        ImportUshahidiV2\Mappers\MessageMapper $mapper
    ) {
        // Set up importer
        $importer = new ImportUshahidiV2\Importer(
            'message',
            $mapper,
            $mappingRepo,
            $destRepo
        );

        // We have to import parents and children in 2 steps to ensure the parents are all created
        // before their children and parent_id can be mapped to the new parent
        $this->importParents($importer);
        $this->importChildren($importer);
    }

    protected function importParents($importer)
    {
        $imported = 0;
        $batch = 0;
        $last_id = -1;

        // Know the max id
        $max_id = $this->getConnection()
            ->table('message')
            ->select(DB::raw('max(id) as max_id'))
            ->where('parent_id', '=', 0)
            ->get()->first()->max_id;

        // While there are data left
        while (true) {
            // Fetch data
            $sourceData = $this->getConnection()
                ->table('message')
                ->select(
                    'message.*',
                    'service_account',
                    'service_name'
                )
                ->where('parent_id', '=', 0)
                ->whereBetween('message.id', [$last_id+1, ($last_id+1) + self::BATCH_SIZE])
                ->leftJoin('reporter', 'message.reporter_id', '=', 'reporter.id')
                ->leftJoin('service', 'reporter.service_id', '=', 'service.id')
                #->orderBy('message.id', 'asc')
                ->get();
            
            if (!$sourceData->isEmpty()) {
                $created = $importer->run($this->getImport(), $sourceData);
            }

            // jump to the next id batch
            $last_id = ($last_id+1) + self::BATCH_SIZE;
            if ($last_id > $max_id) {
                // Break out of the loop
                break;
            }

            $batch++;
        }
    }

    protected function importChildren($importer)
    {
        $imported = 0;
        $batch = 0;
        // While there are data left
        while (true) {
            // Fetch data
            $sourceData = $this->getConnection()
                ->table('message')
                ->select(
                    'message.*',
                    'service_account',
                    'service_name'
                )
                ->where('parent_id', '<>', 0)
                ->leftJoin('reporter', 'message.reporter_id', '=', 'reporter.id')
                ->leftJoin('service', 'reporter.service_id', '=', 'service.id')
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('message.id', 'asc')
                ->get();

            // If there is no more data
            if ($sourceData->isEmpty()) {
                // Break out of the loop
                break;
            }

            $created = $importer->run($this->getImport(), $sourceData);

            $batch++;
        }
    }
}
