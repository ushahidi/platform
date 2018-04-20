<?php

namespace Tests\Unit\API;

use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\TestCase;
use Faker;

/**
 * @group api
 * @group integration
 */
class ExportJobsExternalAPITest extends TestCase
{

    protected $jobId;
    protected $userId;

    public function setUp()
    {
        parent::setUp();

        $this->withoutMiddleware();
        $faker = Faker\Factory::create();

        $this->userId = service('repository.user')->create(new \Ushahidi\Core\Entity\User([
            'email' => $faker->email,
            'password' => $faker->password(10),
            'realname' => $faker->name,
        ]));

        $exportJobs = service('repository.export_job');
        $this->jobId = $exportJobs->create(new \Ushahidi\Core\Entity\ExportJob([
            'user_id' => $this->userId,
            'entity_type' => 'post'
        ]));
    }

    public function tearDown()
    {
        parent::tearDown();

        service('repository.user')->delete(new \Ushahidi\Core\Entity\User(['id' => $this->userId]));
        service('repository.export_job')->delete(new \Ushahidi\Core\Entity\ExportJob(['id' => $this->jobId]));
    }

    /**
     * Get count
     */
    public function testCount()
    {
        $this->get('/api/v3/exports/jobs/external/count/'. $this->jobId);

        $this->seeStatusCode('200')
            ->seeJsonStructure([[
                "total",
                "label",
            ]]);
    }

    /**
     * Get count
     */
    public function testCli()
    {
        $this->get('/api/v3/exports/jobs/external/cli/'. $this->jobId);

        $this->seeStatusCode('200')
            ->seeJsonStructure([
                "results" => [[
                    'file',
                ]]
            ]);
    }

    /**
     * Get all jobs
     */
    public function testGetJobs()
    {
        $this->get('/api/v3/exports/jobs/external/jobs');

        $this->seeStatusCode('200')
            ->seeJsonStructure([
                "count",
                "results" => [
                    '*' => [
                        'id',
                        'user',
                        'entity_type',
                        'fields',
                        'filters',
                        'status',
                        'header_row',
                        'created'
                    ]
                ],
            ]);
    }


    /**
     * Getting a job
     */
    public function testGetJob()
    {
        $this->get('/api/v3/exports/jobs/external/jobs/'. $this->jobId);

        $this->seeStatusCode('200')
            ->seeJsonStructure([
                'id',
                'user',
                'entity_type',
                'fields',
                'filters',
                'status',
                'header_row',
                'created'
            ]);
    }


    /**
     * Update a job
     */
    public function testUpdateJob()
    {
        $this->json('PUT', '/api/v3/exports/jobs/external/jobs/'. $this->jobId, [
            'filters' => ['status' => 'draft']
        ]);

        $this->seeStatusCode('200')
            ->seeJsonStructure([
                'id',
                'user',
                'entity_type',
                'fields',
                'filters',
                'status',
                'header_row',
                'created'
            ])
            ->seeJson([
                'filters' => [
                    'status' => 'draft',
                ],
            ]);
    }
}
