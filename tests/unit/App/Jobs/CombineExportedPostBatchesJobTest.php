<?php

namespace Tests\Unit\App\Jobs;

use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;
use Faker;
use Mockery as M;

use Ushahidi\App\Jobs\CombineExportedPostBatchesJob;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Core\Entity\ExportBatch;
use Ushahidi\Core\Entity\ExportBatchRepository;
use Illuminate\Support\Facades\Storage;

/**
 * @group api
 * @group integration
 */
class CombineExportedPostBatchesJobTest extends TestCase
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

    public function testCombineBatchesJob()
    {
        $jobId = 33;
        $job = new CombineExportedPostBatchesJob($jobId);

        $exportJobRepo = M::mock(ExportJobRepository::class);
        $exportBatchRepo = M::mock(ExportBatchRepository::class);

        $exportJob = new ExportJob([
                'id' => $jobId,
                'status' => 'pending'
            ]);
        // Loads jobs
        $exportJobRepo->shouldReceive('get')
            ->with($jobId)
            ->once()
            ->andReturn($exportJob);

        // Check job is done
        $exportJobRepo->shouldReceive('areBatchesFinished')
            ->with($jobId)
            ->once()
            ->andReturn(true);

        $exportBatchRepo->shouldReceive('getByJobId')
            ->with($jobId)
            ->once()
            ->andReturn(collect([
                new ExportBatch([
                    'filename' => 'batch2.csv',
                    'batch_number' => 2,
                    'has_header' => 0
                ]),
                new ExportBatch([
                    'filename' => 'batch0.csv',
                    'batch_number' => 0,
                    'has_header' => 1
                ]),
                new ExportBatch([
                    'filename' => 'batch1.csv',
                    'batch_number' => 1,
                    'has_header' => 0
                ]),
            ]));

        Storage::fake();
        Storage::put('batch0.csv', "header\nLine0\nLine1\n");
        Storage::put('batch1.csv', "Line2\nLine3\n");
        Storage::put('batch2.csv', "Line4\nLine5\n");

        // Updates job
        $exportJobRepo->shouldReceive('update')
            ->with($exportJob)
            ->once()
            ->andReturn(1);

        $job->handle($exportJobRepo, $exportBatchRepo);

        // Sets job status to completed
        $this->assertEquals('EXPORTED_TO_CDN', $exportJob->status);

        // There should only be 1 output file
        $this->assertCount(1, Storage::allFiles());

        // The output file should match the URL (once we drop the storage/ prefix)
        $outputFile = str_replace('storage/', '', $exportJob->url);
        Storage::assertExists($outputFile);

        $this->assertEquals(
            "header\nLine0\nLine1\nLine2\nLine3\nLine4\nLine5\n",
            Storage::get($outputFile)
        );
    }

    public function testCombineBatchesNotReady()
    {
        $jobId = 33;
        $job = new CombineExportedPostBatchesJob($jobId);

        $exportJobRepo = M::mock(ExportJobRepository::class);
        $exportBatchRepo = M::mock(ExportBatchRepository::class);

        $exportJob = new ExportJob([
                'id' => $jobId,
                'status' => 'pending'
            ]);
        // Loads jobs
        $exportJobRepo->shouldReceive('get')
            ->with($jobId)
            ->once()
            ->andReturn($exportJob);

        // Check job is done
        $exportJobRepo->shouldReceive('areBatchesFinished')
            ->with($jobId)
            ->once()
            ->andReturn(false);

        $dispatcher = $this->mockDispatcher();
        $dispatcher->shouldReceive('dispatch')->once()
                ->with(M::type(\Ushahidi\App\Jobs\CombineExportedPostBatchesJob::class));

        $job->handle($exportJobRepo, $exportBatchRepo);
    }

    public function testJobAlreadyComplete()
    {
        $jobId = 33;
        $job = new CombineExportedPostBatchesJob($jobId);

        $exportJobRepo = M::mock(ExportJobRepository::class);
        $exportBatchRepo = M::mock(ExportBatchRepository::class);

        $exportJob = new ExportJob([
                'id' => $jobId,
                'status' => 'SUCCESS'
            ]);
        // Loads jobs
        $exportJobRepo->shouldReceive('get')
            ->with($jobId)
            ->once()
            ->andReturn($exportJob);

        $job->handle($exportJobRepo, $exportBatchRepo);
    }
}
