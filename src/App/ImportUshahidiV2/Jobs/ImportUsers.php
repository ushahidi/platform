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

    const BATCH_SIZE = 5;

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
                ->limit(self::BATCH_SIZE)
                ->offset($batch * self::BATCH_SIZE)
                ->orderBy('id', 'asc')
                ->get();

            // If there are no more users
            if ($sourceUsers->isEmpty()) {
                // Break out of the loop
                break;
            }

            // Save users
            $destUsers = $sourceUsers->each(function ($item) use ($userMapper, $userRepo, $mappingRepo) {
                $user = $userMapper((array) $item);

                $id = $userRepo->create($user);

                // Save form --> survey mapping
                $mappingRepo->create(new ImportUshahidiV2\ImportMapping([
                    'import_id' => $this->importId,
                    'source_type' => 'user',
                    'source_id' => $item->id,
                    'dest_type' => 'user',
                    'dest_id' => $id,
                ]));
            });

            // Add to count
            $importedUsers += $destUsers->count();
            $batch++;
        }
    }
}
