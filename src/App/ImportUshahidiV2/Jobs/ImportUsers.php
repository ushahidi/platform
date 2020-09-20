<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;

class ImportUsers extends ImportUshahidiV2Job
{
    use Concerns\ConnectsToV2DB;

    const BATCH_SIZE = 1000;

    protected $dbConfig;
    protected $mappingRepo;
    protected $destRepo;

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
     * Create temporary table for joining. The purpose is to simplify and speed up
     * the main query for this class.
     */
    private function collectUserToRolelistTable()
    {
        $this->getConnection()->insert(
            DB::RAW("
                CREATE TEMPORARY TABLE user_to_rolelist 
                (UNIQUE user_id (user_id))
                select roles_users.`user_id` as user_id, GROUP_CONCAT(roles.name) AS role_names
                from roles_users
                join roles on roles_users.role_id = roles.id
                group by roles_users.user_id;
            ")
        );
    }

    private function cleanup()
    {
        $this->getConnection()->unprepared(
            DB::RAW("DROP TEMPORARY TABLE user_to_rolelist")
        );
    }

    protected function handleExisting(Collection $sourceData)
    {
        //
        $grouped = $sourceData->mapToGroups(function ($item) {
            Log::debug("Processing user {}", [$item]);
            $match = $this->destRepo->getByEmail($item->email);
            if ($match->id) {
                Log::debug("Matched to {}", [$match]);
                return [
                    'existing' => new ImportUshahidiV2\ImportMapping([
                        'import_id' => $this->importId,
                        'source_type' => 'user',
                        'source_id' => $item->id,
                        'dest_type' => 'users',
                        'dest_id' => $match->id,
                        'established_by' => 'duplicate-detection',
                    ])
                ];
            } else {
                return [ 'new' => $item ];
            }
        });

        // Create mapping entries for the matched emails
        if ($grouped->has('existing')) {
            $this->mappingRepo->createMany($grouped['existing']);
        }

        // Return the new data to continue the import process with it
        if ($grouped->has('new')) {
            return $grouped['new'];
        } else {
            return new Collection();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ImportUshahidiV2\Contracts\ImportMappingRepository $mappingRepo,
        Entity\UserRepository $destRepo,
        ImportUshahidiV2\Mappers\UserMapper $mapper
    ) {
        // Set up importer
        $importer = new ImportUshahidiV2\Importer(
            'user',
            $mapper,
            $mappingRepo,
            $destRepo
        );

        $this->mappingRepo = $mappingRepo;
        $this->destRepo = $destRepo;

        // Set up temporal clean incident_person table
        $this->collectUserToRolelistTable();

        $batch = 0;
        // While there are users left
        while (true) {
            $sourceUsers = $this->getConnection()
                ->table('users')
                ->select('users.*', 'user_to_rolelist.role_names as role')
                ->leftJoin('user_to_rolelist', 'users.id', '=', 'user_to_rolelist.user_id')
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('id', 'asc')
                ->get();

            // If there are no more users
            if ($sourceUsers->isEmpty()) {
                // Break out of the loop
                break;
            }

            $sourceUsers = $this->handleExisting($sourceUsers);

            $created = $importer->run($this->getImport(), $sourceUsers);

            $batch++;
        }

        $this->cleanup();
    }
}
