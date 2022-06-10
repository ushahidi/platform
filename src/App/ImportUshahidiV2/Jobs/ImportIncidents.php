<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\DB;
use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;

class ImportIncidents extends ImportUshahidiV2Job
{
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
     * Create temporary clone of incident_person table on the v2 db server.
     * The purpose is ensuring that there are no repetitions in the incident_id
     * column, which was supposed to be unique but didn't have a unique index on it.
     */
    private function dealiaseIncidentPersonTable()
    {
        $this->getConnection()->insert(
            DB::RAW("
                CREATE TEMPORARY TABLE incident_person_clean 
                (UNIQUE ipc_incident_id (incident_id))
                select ip.*
                from incident_person as ip
                inner join (
                    select max(id) as max_id, incident_id
                    from incident_person
                    group by incident_id
                ) ipd
                ON ip.id = ipd.max_id
            ")
        );
    }

    /**
     * Create temporary table for joining. The purpose is to simplify and speed up
     * the main query for this class.
     */
    private function collectIncidentToCategorylistTable()
    {
        $this->getConnection()->insert(
            DB::RAW("
                CREATE TEMPORARY TABLE incident_to_categorylist 
                (UNIQUE incident_id (incident_id))
                select incident_id, GROUP_CONCAT(category_id) AS categories
                from incident_category
                group by incident_id;
            ")
        );
    }

    private function cleanup()
    {
        $this->getConnection()->unprepared(
            DB::RAW("DROP TEMPORARY TABLE incident_person_clean")
        );
        $this->getConnection()->unprepared(
            DB::RAW("DROP TEMPORARY TABLE incident_to_categorylist")
        );
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

        // Set up temporary tables to aid/optimise querying
        $this->dealiaseIncidentPersonTable();
        $this->collectIncidentToCategorylistTable();

        $batch = 0;
        // While there are data left
        while (true) {
            // Fetch data
            $sourceData = $this->getConnection()
                ->table('incident')
                ->select(
                    'incident.*',
                    'incident_to_categorylist.categories',
                    'location_name',
                    'latitude',
                    'longitude',
                    'incident_person_clean.person_first',
                    'incident_person_clean.person_last',
                    'incident_person_clean.person_email'
                )
                ->leftJoin('incident_to_categorylist', 'incident.id', '=', 'incident_to_categorylist.incident_id')
                ->leftJoin('incident_person_clean', 'incident.id', '=', 'incident_person_clean.incident_id')
                ->leftJoin('location', 'incident.location_id', '=', 'location.id')
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

            // Fetch geometry data for incidents
            $geometryData = $this->getConnection()
                ->table('geometry')
                ->select('geometry.*', DB::RAW('AsText(`geometry`.geometry) as geometry_astext'))
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
                    'form_response.*',
                    'form_field.field_type',
                    'form_field.field_isdate',
                    'datatype.option_value AS field_datatype'
                )
                // Load all form responses for this batch of incidents
                ->whereIn('incident_id', $sourceData->pluck('id')->all())
                ->leftJoin('form_field', 'form_response.form_field_id', '=', 'form_field.id')
                ->leftJoin('form_field_option as datatype', function ($join) {
                    $join->on('datatype.form_field_id', '=', 'form_field.id');
                    $join->where('datatype.option_name', '=', 'field_datatype');
                })
                ->orderBy('incident_id', 'asc')
                ->orderBy('id', 'asc')
                ->get()
                // Group returned collection by incident id
                ->groupBy('incident_id');

            // Merge the media and form responses into the incidents
            $sourceData->each(function ($incident) use ($mediaData, $formResponseData, $geometryData) {
                $incident->media = $mediaData->get($incident->id);
                $incident->form_responses = $formResponseData->get($incident->id);
                $incident->geometries = $geometryData->get($incident->id);
            });

            $created = $importer->run($this->getImport(), $sourceData);

            $batch++;
        }

        $this->cleanup();
    }
}
