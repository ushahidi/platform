<?php
 namespace Ushahidi\App\Jobs;

use Ushahidi\Core\Usecase\CSV\ImportCSVPostsUsecase;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository;
use Illuminate\Support\Facades\Log;

class ImportPostsJob extends Job
{
    protected $csvId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($csvId)
    {
        $this->csvId = $csvId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ImportCSVPostsUsecase $usecase)
    {
        $usecase->setIdentifiers(['id' => $this->csvId]);
        /**
         * Step two of import.
         * Support all line endings without manually specifying it
         * (primarily added because of OS9 line endings which do not work by default )
         */
        ini_set('auto_detect_line_endings', 1);


        $results = $usecase->interact();
    }
}
