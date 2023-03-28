<?php

namespace Ushahidi\Modules\V3\Jobs\Concerns;

use Exception;
use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Core\Ohanzee\Entities\ExportJob;

trait RecordsExportJobFailure
{
    protected $jobId;

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        $exportJobRepo = resolve(ExportJobRepository::class);
        // Set status failed
        $job = $exportJobRepo->get($this->jobId);
        $job->setState([
            'status' => ExportJob::STATUS_FAILED,
        ]);
        $exportJobRepo->update($job);
    }
}
