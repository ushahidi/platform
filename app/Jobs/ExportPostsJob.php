<?php

namespace Ushahidi\App\Jobs;

use Exception;
use Ushahidi\Core\Usecase\Export\Job\PostCount;
use Ushahidi\Core\Entity\ExportJobRepository;
use Illuminate\Support\Facades\Log;

class ExportPostsJob extends Job
{
    protected $batchSize = 200;

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

        $totalBatches = ceil($totalRows / $this->batchSize);

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

        // Set status = queued
        $job->setState([
            'total_batches' => $totalBatches, // Add 1 because it was zero indexed
            'total_rows' => $totalRows,
            'status' => 'queued' // Check expected value, move to constant
        ]);
        // @todo add to count usecase?
        $exportJobRepo->update($job);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $exportJobRepo = app(ExportJobRepository::class);
        // Set status failed
        $job = $exportJobRepo->get($this->jobId);
        $job->setState([
            'status' => 'failed'
        ]);
        $exportJobRepo->update($job);
    }
}
