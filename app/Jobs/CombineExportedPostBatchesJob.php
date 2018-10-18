<?php

namespace Ushahidi\App\Jobs;

use Exception;
use Log;

class CombineExportedPostBatchesJob extends Job
{
    protected $jobId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug("Handle things", [$this->jobId]);
        // Load job
        // Check if all batches exported
            // if not requeue
        // Load batches
        // Combine files
        // Set status
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Set status failed
    }
}
