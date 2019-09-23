<?php

namespace Ushahidi\App\Jobs;

use Ushahidi\Core\Usecase\Export\Job\PostCount;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository;
use Illuminate\Support\Facades\Log;

class ExportPostsJob extends Job
{
    use RecordsExportJobFailure;

    protected $batchSize;

    protected $jobId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobId)
    {
        $this->jobId = $jobId;
        $this->batchSize = config('media.csv_batch_size', 200);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PostCount $usecase, ExportJobRepository $exportJobRepo)
    {
        // Load job
        $job = $exportJobRepo->get($this->jobId);

        // Get total posts count
        // @todo this probably doesn't need its own usecase
        $usecase->setIdentifiers(['id' => $this->jobId]);
        $results = $usecase->interact();
        $totalRows = $results[0]['total'];

        Log::debug('Got job count', ['jobId' => $this->jobId, 'results' => $results]);

        // If count is zero. Mark as failed
        if ($totalRows == 0) {
            // Set status = failed
            $job->setState([
                'total_batches' => 0,
                'total_rows' => 0,
                'status' => ExportJob::STATUS_FAILED,
            ]);
            $exportJobRepo->update($job);
            // All done
            return;
        }

        $totalBatches = ceil($totalRows / $this->batchSize);

        // Set status = queued
        $job->setState([
            'total_batches' => $totalBatches,
            'total_rows' => $totalRows,
            'status' => ExportJob::STATUS_QUEUED,
        ]);
        // @todo add to count usecase?
        $exportJobRepo->update($job);

        // Generate other meta data here too? ie. header rows
        //
        Log::debug('Queuing batches', ['totalBatches' => $totalBatches]);
        for ($batchNumber = 0; $batchNumber < $totalBatches; $batchNumber++) {
            Log::debug('Queuing batch', [
                'jobId' => $this->jobId,
                'batchNumber' => $batchNumber,
                'offset' => $batchNumber * $this->batchSize,
                'limit' => $this->batchSize
            ]);
            dispatch(new ExportPostsBatchJob(
                $this->jobId,
                $batchNumber,
                $batchNumber * $this->batchSize,
                $this->batchSize,
                ($batchNumber === 0)
            ));
        }
    }
}
