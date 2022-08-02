<?php

namespace Ushahidi\App\V3\Jobs;

use Ushahidi\Core\Tools\Job;
use Illuminate\Support\Facades\Log;
use Ushahidi\Factory\UsecaseFactory;
use Ushahidi\Core\Concerns\RecordsExportJobFailure;
use Ushahidi\Contracts\Repository\Entity\ExportJobRepository;

class ExportPostsBatchJob extends Job
{
    use RecordsExportJobFailure;

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
    public function handle(UsecaseFactory $factory, ExportJobRepository $exportJobRepo)
    {
        $usecase = $factory
            ->get('posts_export', 'export')
            ->setAuthorizer(service('authorizer.export_job'))
            ->setFilters([
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
