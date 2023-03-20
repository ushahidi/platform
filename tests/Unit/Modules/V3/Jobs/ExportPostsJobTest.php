<?php
namespace Ushahidi\Tests\Unit\Modules\V3\Jobs;

use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Modules\V3\Jobs\ExportPostsJob;
use Ushahidi\Modules\V3\Jobs\ExportPostsBatchJob;
use Ushahidi\Core\Usecase\Export\Job\PostCount;
use Ushahidi\Contracts\Repository\Entity\ExportJobRepository;

/**
 * @group api
 * @group integration
 */
class ExportPostsJobTest extends TestCase
{
    protected function mockDispatcher()
    {
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
                ->with(M::type(ExportPostsBatchJob::class));

        $usecase = M::mock(PostCount::class);
        $exportJobRepo = M::mock(ExportJobRepository::class);

        $usecase->shouldReceive('setIdentifiers')
            ->once()
            ->with([
                'id' => $jobId,
            ]);

        $usecase->shouldReceive('interact')
            ->once()
            ->andReturn([
                ['total' => 830, 'label' => 'all'],
            ]);

        $exportJob = new ExportJob([
            'id' => $jobId,
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

        $exportJobRepo = M::mock(ExportJobRepository::class);

        $exportJob = new ExportJob([
            'id' => $jobId,
        ]);
        $exportJobRepo->shouldReceive('get')
            ->with($jobId)
            ->once()
            ->andReturn($exportJob);

        $exportJobRepo->shouldReceive('update')
            ->with($exportJob)
            ->once();

        // Inject mocks into the app
        $this->app->instance(
            ExportJobRepository::class,
            $exportJobRepo
        );

        $job = new ExportPostsJob($jobId);
        $job->failed(new \RuntimeException('I broke it'));

        $this->assertEquals('FAILED', $exportJob->status);
    }
}
