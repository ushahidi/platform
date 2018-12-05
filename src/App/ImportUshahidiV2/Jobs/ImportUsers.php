<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Ushahidi\App\Jobs\Job;
use Ushahidi\Core\Entity;
use Ushahidi\App\ImportUshahidiV2;

class ImportUsers extends Job
{

    const BATCH_SIZE = 50;

    protected $importId;
    protected $dbConfig;
    protected $dbConnection;

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

    protected function getConnection()
    {
        if (!$this->dbConnection) {
            // Configure database
            config(['database.connections.importv2' => $this->dbConfig]);

            return $this->dbConnection = DB::connection('importv2');
        }

        return $this->dbConnection;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ImportUshahidiV2\Contracts\ImportMappingRepository $mappingRepo,
        Entity\UserRepository $userRepo,
        ImportUshahidiV2\Mappers\UserMapper $userMapper
    ) {
        $importedUsers = 0;
        $batch = 0;
        // While there are users left
        while (true) {
            // Fetch users
            $sourceUsers = $this->getConnection()
                ->table('users')
                ->select('users.*', DB::raw('GROUP_CONCAT(`roles`.`name`) AS role'))
                ->join('roles_users', 'users.id', '=', 'roles_users.user_id')
                ->join('roles', 'roles.id', '=', 'roles_users.role_id')
                ->groupBy('users.id')
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('id', 'asc')
                ->get();

            // If there are no more users
            if ($sourceUsers->isEmpty()) {
                // Break out of the loop
                break;
            }

            // Transform users
            $destUsers = $sourceUsers->map(function ($item) use ($userMapper) {
                return $userMapper((array) $item);
            });

            // Save users
            $inserted = $userRepo->createMany($destUsers);

            // Match source and destination ids
            $mappings = $sourceUsers->pluck('id')->combine($inserted)->map(function ($item, $key) {
                return new ImportUshahidiV2\ImportMapping([
                    'import_id' => $this->importId,
                    'source_type' => 'user',
                    'source_id' => $key,
                    'dest_type' => 'user',
                    'dest_id' => $item,
                ]);
            });

            // Save mappings
            $mappingRepo->createMany($mappings);

            // Add to count
            $importedUsers += $destUsers->count();
            $batch++;
        }
    }
}
