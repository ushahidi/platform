<?php

namespace Ushahidi\App\Jobs;

use Exception;
use Log;
use Ushahidi\Factory\UsecaseFactory;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Core\Usecase\Post\Export;

class ExportPostsBatchJob extends Job
{
    protected $jobId;
    protected $batchNumber;
    protected $offset;
    protected $limit;
    protected $includeHeader;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobId, $batchNumber, $offset, $limit, $includeHeader)
    {
        $this->jobId = $jobId;
        $this->batchNumber = $batchNumber;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->includeHeader = $includeHeader;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Export $usecase, ExportJobRepository $exportJobRepo)
    {
        // Get the usecase and pass in authorizer, payload and transformer
        $usecase
            ->setFilters([
                'limit' => $this->limit,
                'offset' => $this->offset,
                'add_header' => $this->includeHeader
            ])
            ->setIdentifiers([
                'job_id' => $this->jobId,
                'batch_number' => $this->batchNumber
            ]);

        $batch = $usecase->interact();

        Log::debug('Batch completed', [$batch]);

        // Check for success!?!
        // Or can I just ignore it and let exceptions do there thing?

        // Check if job is finished
        if ($exportJobRepo->isJobFinished($this->jobId)) {
            Log::debug('All batches finished', ['jobId' => $this->jobId]);
            // if yes, queue combine job
            dispatch(new CombineExportedPostBatchesJob($this->jobId));
        }
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
            'status' => ExportJob::STATUS_FAILED
        ]);
        $exportJobRepo->update($job);
    }
}
