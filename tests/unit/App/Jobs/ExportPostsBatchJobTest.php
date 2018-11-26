<?php

namespace Tests\Unit\App\Jobs;

use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;
use Faker;
use Mockery as M;

use Ushahidi\App\Jobs\ExportPostsBatchJob;
use Ushahidi\App\Jobs\CombineExportedPostBatchesJob;
use Ushahidi\Core\Entity\ExportJob;

/**
 * @group api
 * @group integration
 */
class ExportPostsBatchJobTest extends TestCase
{
    public function testExportPostsBatchJob()
    {
        $jobId = 33;
        $batchNumber = 2;
        $offset = 200;
        $limit = 200;
        $includeHeader = true;
        $job = new ExportPostsBatchJob($jobId, $batchNumber, $offset, $limit, $includeHeader);

        $usecase = M::mock(\Ushahidi\Core\Usecase\Post\Export::class);
        $exportJobRepo = M::mock(\Ushahidi\Core\Entity\ExportJobRepository::class);

        $usecase->shouldReceive('setFilters')
            ->once()
            ->with([
                'limit' => $limit,
                'offset' => $offset,
                'add_header' => $includeHeader
            ])
            ->andReturn($usecase);

        $usecase->shouldReceive('setIdentifiers')
            ->once()
            ->with([
                'job_id' => $jobId,
                'batch_number' => $batchNumber
            ])
            ->andReturn($usecase);

        $usecase->shouldReceive('interact')
            ->once()
            ->andReturn([
                [
                    'filename' => 'filename.jpg',
                    'id' => 55,
                    'rows' => 200,
                    'status' => 'completed'
                ]
            ]);

        $job->handle($usecase, $exportJobRepo);
    }
}
