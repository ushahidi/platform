<?php
 namespace Ushahidi\App\Jobs;

use Ushahidi\Core\Usecase\CSV\ImportCSVPostsUsecase;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository;
use Illuminate\Support\Facades\Log;

class ImportPostsJob extends Job
{
    protected $csvId;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($csvId, $userId)
    {
        $this->csvId = $csvId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ImportCSVPostsUsecase $usecase)
    {
        /**
         * Step two of import.
         * Support all line endings without manually specifying it
         * (primarily added because of OS9 line endings which do not work by default )
         */
        ini_set('auto_detect_line_endings', 1);

        $usecase->setIdentifiers([
            'id' => $this->csvId,
            'user_id' => $this->userId
        ]);

        $results = $usecase->interact();
    }
}
