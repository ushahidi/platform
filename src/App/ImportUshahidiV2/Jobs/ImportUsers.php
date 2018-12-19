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
        ImportUshahidiV2\Mappers\UserMapper $mapper
    ) {
        // Set up importer
        $importer = new ImportUshahidiV2\Importer(
            'user',
            $mapper,
            $mappingRepo,
            $destRepo
        );

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

            $created = $importer->run($this->importId, $sourceUsers);

            $batch++;
        }
    }
}
