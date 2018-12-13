<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\DB;
use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;

class ImportReporters extends Job
{
    use Concerns\ConnectsToV2DB;

    const BATCH_SIZE = 50;

    protected $importId;
    protected $dbConfig;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $importId, array $dbConfig)
    {
        $this->importId = $importId;
        $this->dbConfig = $dbConfig;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ImportUshahidiV2\Contracts\ImportMappingRepository $mappingRepo,
        Entity\UserRepository $destRepo,
        ImportUshahidiV2\Mappers\ReporterUserMapper $mapper
    ) {
        // Set up importer
        $importer = new ImportUshahidiV2\Importer(
            'reporter',
            $mapper,
            $mappingRepo,
            $destRepo
        );

        $batch = 0;
        // While there are data left
        while (true) {
            // Fetch data
            $sourceData = $this->getConnection()
                ->table('reporter')
                ->select(
                    'reporter.*',
                    'location_name',
                    'latitude',
                    'longitude',
                    'service_name',
                    'level_title'
                )
                ->leftJoin('service', 'reporter.service_id', '=', 'service.id')
                ->leftJoin('level', 'reporter.level_id', '=', 'level.id')
                ->leftJoin('location', 'reporter.location_id', '=', 'location.id')
                // @todo match contact to user for reporters that are excluded
                ->where('service_account', 'not in', $this->getConnection()->table('users')->select('email'))
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('id', 'asc')
                ->get();

            // If there is no more data
            if ($sourceData->isEmpty()) {
                // Break out of the loop
                break;
            }

            $created = $importer->run($this->importId, $sourceData);

            $batch++;
        }
    }
}
