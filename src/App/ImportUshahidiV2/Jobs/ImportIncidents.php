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
                ->leftJoin('incident_person', function($query){
                    // ensure only one incident_person is taken,
                    // as there are no constraints in v2 databases
                    // to prevent multiple persons for the same incident
                    $query->on('incident_person.id', '=', DB::RAW("(
                        SELECT ip.id
      	                FROM incident_person AS ip
      	                WHERE incident.id = ip.incident_id
      	                ORDER BY ip.id DESC
      	                LIMIT 1
                    )"));
                })
                ->leftJoin('location', 'incident.location_id', '=', 'location.id')
                ->groupBy('incident.id')
                ->groupBy('incident_person.id')
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('id', 'asc')
                ->get();

            // If there is no more data
            if ($sourceData->isEmpty()) {
                // Break out of the loop
                break;
            }

            // Fetch media for incidents
            $mediaData = $this->getConnection()
                ->table('media')
                ->select(
                    'media.*'
                )
                // Load all media items for this batch of incidents
                ->whereIn('incident_id', $sourceData->pluck('id')->all())
                ->orderBy('incident_id', 'asc')
                ->orderBy('id', 'asc')
                ->get()
                // Group returned collection by incident id
                ->groupBy('incident_id');

            // Fetch custom form responses for incidents
            $formResponseData = $this->getConnection()
                ->table('form_response')
                ->select(
                    'form_response.*'
                )
                // Load all form responses for this batch of incidents
                ->whereIn('incident_id', $sourceData->pluck('id')->all())
                ->orderBy('incident_id', 'asc')
                ->orderBy('id', 'asc')
                ->get()
                // Group returned collection by incident id
                ->groupBy('incident_id');

            // Merge the media and form responses into the incidents
            $sourceData->each(function ($incident) use ($mediaData, $formResponseData) {
                $incident->media = $mediaData->get($incident->id);
                $incident->form_responses = $formResponseData->get($incident->id);
            });

            $created = $importer->run($this->importId, $sourceData);

            $batch++;
        }
    }
}
