<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\DB;
use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;

class ImportIncidents extends Job
{
    use Concerns\ConnectsToV2DB;

    const BATCH_SIZE = 200;

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
        Entity\PostRepository $destRepo,
        ImportUshahidiV2\Mappers\IncidentPostMapper $mapper
    ) {
        // Set up importer
        $importer = new ImportUshahidiV2\Importer(
            'incident',
            $mapper,
            $mappingRepo,
            $destRepo
        );

        $imported = 0;
        $batch = 0;
        // While there are data left
        while (true) {
            // Fetch data
            $sourceData = $this->getConnection()
                ->table('incident')
                ->select(
                    'incident.*',
                    DB::raw('GROUP_CONCAT(`category_id`) AS categories'),
                    'location_name',
                    'latitude',
                    'longitude',
                    'person_first',
                    'person_last',
                    'person_email'
                )
                ->leftJoin('incident_category', 'incident.id', '=', 'incident_category.incident_id')
                ->leftJoin('incident_person', 'incident.id', '=', 'incident_person.incident_id')
                ->leftJoin('location', 'incident.location_id', '=', 'location.id')
                ->groupBy('incident.id')
                ->groupBy('incident_person.id')
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('id', 'asc')
                ->get();

            // $incidentMediaReader = new Reader\PdoReader(
            //     $connection,
            //     "SELECT media.*
            //         FROM media
            //         WHERE incident_id IS NOT NULL
            //         AND media_type = 4
            //         ORDER BY incident_id ASC
            //         "
            // );


            // If there is no more data
            if ($sourceData->isEmpty()) {
                // Break out of the loop
                break;
            }

            $created = $importer->run($this->importId, $sourceData);

            // Add to count
            $imported += $created;
            $batch++;
        }
    }
}
