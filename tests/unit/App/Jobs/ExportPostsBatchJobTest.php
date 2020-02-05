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
    protected function mockDispatcher()
    {
        unset($this->app->availableBindings['Illuminate\Contracts\Bus\Dispatcher']);
        $mock = M::mock('Illuminate\Bus\Dispatcher[dispatch]', [$this->app]);
        $this->app->instance(
            'Illuminate\Contracts\Bus\Dispatcher',
            $mock
        );

        return $mock;
    }

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

        $exportJobRepo->shouldReceive('areBatchesFinished')
            ->with($jobId)
            ->once()
            ->andReturn(false);

        $job->handle($usecase, $exportJobRepo);
    }

    public function testExportFinalBatch()
    {
        $jobId = 33;
        $batchNumber = 2;
        $offset = 200;
        $limit = 200;
        $includeHeader = true;
        $job = new ExportPostsBatchJob($jobId, $batchNumber, $offset, $limit, $includeHeader);

        $dispatcher = $this->mockDispatcher();
        $dispatcher->shouldReceive('dispatch')->once()
                ->with(M::type(\Ushahidi\App\Jobs\CombineExportedPostBatchesJob::class));

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

        $exportJobRepo->shouldReceive('areBatchesFinished')
            ->with($jobId)
            ->once()
            ->andReturn(true);

        $job->handle($usecase, $exportJobRepo);
    }
}
