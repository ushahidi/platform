<?php

namespace Ushahidi\Modules\V3\Jobs;

use Ushahidi\Core\Tool\Job;
use Illuminate\Support\Facades\Log;
use Ushahidi\Core\Usecase\Post\ExportPost as Export;
use Ushahidi\Core\Entity\ExportJobRepository;

class ExportPostsBatchJob extends Job
{
    use Concerns\RecordsExportJobFailure;

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
        $usecase->setFilters([
            'limit' => $this->limit,
            'offset' => $this->offset,
            'add_header' => $this->includeHeader,
        ])
            ->setIdentifiers([
                'job_id' => $this->jobId,
                'batch_number' => $this->batchNumber,
            ]);

        $batch = $usecase->interact();

        Log::debug('Batch completed', [$batch]);

        // Check if batches are finished
        if ($exportJobRepo->areBatchesFinished($this->jobId)) {
            Log::debug('All batches finished', ['jobId' => $this->jobId]);
            // if yes, queue combine job
            dispatch(new CombineExportedPostBatchesJob($this->jobId));
        }
    }
}
