<?php

namespace Ushahidi\Tests\Unit\Core\Usecase\ExportJob;

use Faker;
use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\Contracts\Repository\Entity\ExportJobRepository;
use Ushahidi\Contracts\Session;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Usecase\Export\Job\PostCount;

/**
 * @group api
 * @group integration
 */
class PostCountTest extends TestCase
{
    /**
     * Get count
     */
    public function testCount()
    {
        $jobId = 33;
        $userId = 44;
        $jobRepo = M::mock(ExportJobRepository::class);
        $jobRepo->shouldReceive('get')
            ->once()
            ->with($jobId)
            ->andReturn(new ExportJob([
                'id' => $jobId,
                'user_id' => $userId,
                'entity_type' => 'post',
                'send_to_hdx' => false,
                'send_to_browser' => true,
                'include_hxl' => false,
            ]));
        $jobRepo->shouldReceive('getPostCount')
            ->once()
            ->with($jobId)
            ->andReturn([[
                'total' => 200,
                'label' => 'all',
            ]]);

        $session = M::mock(Session::class);
        $session->shouldReceive('setUser')
            ->once()
            ->with($userId);

        $usecase = new PostCount();
        $usecase->setRepository($jobRepo);
        $usecase->setSession($session);

        $usecase->setIdentifiers([
            'id' => $jobId,
        ]);

        $result = $usecase->interact();

        $this->assertArrayHasKey('total', $result[0]);
        $this->assertEquals(200, $result[0]['total']);
        $this->assertArrayHasKey('label', $result[0]);
    }
}
