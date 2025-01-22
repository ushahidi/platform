<?php

namespace Ushahidi\Tests\Feature\V3;

use Faker;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Ushahidi\Tests\TestCase;

/**
 * @group api
 * @group integration
 */
class ExportJobAPI extends TestCase
{
    /**
     * Create a job
     */
    public function testCreateJob()
    {
        $this->withoutMiddleware();
        $this->expectsJobs(\Ushahidi\Modules\V3\Jobs\ExportPostsJob::class);

        $this
            ->actingAs(new \Ushahidi\Authzn\GenericUser(['id' => 2]))
            ->json('POST', '/api/v3/exports/jobs', [
                'fields' => 'test',
                'filters' => [
                    'status' => ['published', 'draft'],
                ],
                'entity_type' => 'post',
                'send_to_browser' =>  true,
                'send_to_hdx' =>  false,
                'hxl_heading_row' => ['something'],
            ]);

        $this->seeStatusCode('200')
            ->seeJson([
                'fields' => ['test'],
                'filters' => [
                    'status' => ['published', 'draft'],
                ],
                'entity_type' => 'post',
                'send_to_browser' =>  true,
                'send_to_hdx' =>  false,
                'hxl_heading_row' => null,
            ]);
    }

    /**
     * Create a job
     */
    public function testCreateJobWithRealDispatch()
    {
        $this->withoutMiddleware();

        $this
            ->actingAs(new \Ushahidi\Modules\Auth\GenericUser(['id' => 2]))
            ->json('POST', '/api/v3/exports/jobs', [
                'fields' => 'test',
                'filters' => [
                    'status' => ['published', 'draft'],
                ],
                'entity_type' => 'post',
                'send_to_browser' =>  true,
                'send_to_hdx' =>  false,
                'hxl_heading_row' => ['something'],
            ]);

        $this->seeStatusCode('200')
            ->seeJson([
                'fields' => ['test'],
                'filters' => [
                    'status' => ['published', 'draft'],
                ],
                'entity_type' => 'post',
                'send_to_browser' =>  true,
                'send_to_hdx' =>  false,
                'hxl_heading_row' => null,
            ]);
    }
}
