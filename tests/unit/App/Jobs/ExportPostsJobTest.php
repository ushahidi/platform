<?php

namespace Tests\Unit\App\Jobs;

use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;
use Faker;
use Mockery as M;

use Ushahidi\App\Jobs\ExportPostsJob;
use Ushahidi\Core\Entity\ExportJob;

/**
 * @group api
 * @group integration
 */
class ExportPostsJobTest extends TestCase
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

    public function testExportPostsJob()
    {
        $jobId = 33;
        $job = new ExportPostsJob($jobId);

        $dispatcher = $this->mockDispatcher();
        $dispatcher->shouldReceive('dispatch')->times(5)
                ->with(M::type(\Ushahidi\App\Jobs\ExportPostsBatchJob::class));

        $usecase = M::mock(\Ushahidi\Core\Usecase\Export\Job\PostCount::class);
        $exportJobRepo = M::mock(\Ushahidi\Core\Entity\ExportJobRepository::class);

        $usecase->shouldReceive('setIdentifiers')
            ->once()
            ->with([
                'id' => $jobId
            ]);

        $usecase->shouldReceive('interact')
            ->once()
            ->andReturn([
                ['total' => 830, 'label' => 'all']
            ]);

        $exportJob = new ExportJob([
                'id' => $jobId
            ]);
        $exportJobRepo->shouldReceive('get')
            ->with($jobId)
            ->once()
            ->andReturn($exportJob);

        $exportJobRepo->shouldReceive('update')
            ->with($exportJob)
            ->once();

        $job->handle($usecase, $exportJobRepo);
        $this->assertEquals(830, $exportJob->total_rows);
        $this->assertEquals(5, $exportJob->total_batches);
        $this->assertEquals('QUEUED', $exportJob->status);
    }

    public function testExportPostsJobErrorHandling()
    {
        $jobId = 33;

        $exportJobRepo = M::mock(\Ushahidi\Core\Entity\ExportJobRepository::class);

        $exportJob = new ExportJob([
                'id' => $jobId
            ]);
        $exportJobRepo->shouldReceive('get')
            ->with($jobId)
            ->once()
            ->andReturn($exportJob);

        $exportJobRepo->shouldReceive('update')
            ->with($exportJob)
            ->once();

        // Inject mocks into the app
        unset($this->app->availableBindings[\Ushahidi\Core\Entity\ExportJobRepository::class]);
        $this->app->instance(
            \Ushahidi\Core\Entity\ExportJobRepository::class,
            $exportJobRepo
        );

        $job = new ExportPostsJob($jobId);
        $job->failed(new \RuntimeException('I broke it'));

        $this->assertEquals('FAILED', $exportJob->status);
    }
}
