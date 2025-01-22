<?php

namespace Ushahidi\Core\Concerns;

use Exception;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Contracts\Repository\Entity\ExportJobRepository;

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
