<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\DB;
use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;

class ImportReporters extends ImportUshahidiV2Job
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
        Entity\ContactRepository $destRepo,
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
        $last_id = -1;

        // Know the max id
        $max_id = $this->getConnection()
            ->table('reporter')
            ->select(DB::raw('max(id) as max_id'))
            ->get()->first()->max_id;

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
                ->whereBetween('reporter.id', [$last_id+1, ($last_id+1) + self::BATCH_SIZE])
                ->leftJoin('service', 'reporter.service_id', '=', 'service.id')
                ->leftJoin('level', 'reporter.level_id', '=', 'level.id')
                ->leftJoin('location', 'reporter.location_id', '=', 'location.id')
                // match contact to user for reporters that are excluded
                // note by @davidlosada: not sure what this is supposed to be doing
                // ->where('service_account',
                //         'not in',
                //         $this->getConnection()->table('users')->select('email'))
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
}
