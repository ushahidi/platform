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
    protected $hxlMetaDataId_1;
    protected $hxlMetaDataId_2;
    protected $hxlLicenseId;

    public function setUp()
    {
        parent::setUp();

        $this->withoutMiddleware();
    }

    /**
     * Moved this out of setup.
     * Helper to set the user role and the job correctly since we
     * override this setup in the test for authorization
     * @param $user_role
     */
    private function setUserAndJob($user_role)
    {
        $faker = Faker\Factory::create();


        $this->userId = service('repository.user')->create(new \Ushahidi\Core\Entity\User([
            'email' => $faker->email,
            'password' => $faker->password(10),
            'realname' => $faker->name,
            'role' => $user_role
        ]));

        $this->hxlLicenseId = service('repository.hxl_license')->create(new \Ushahidi\Core\Entity\HXL\HXLLicense([
            'code' => "ushahidi".rand(),
            'name' => "ushahidi-dataset",
            'link' => "other",
        ]));

        $this->hxlMetaDataId_1 = service('repository.hxl_meta_data')->create(new \Ushahidi\Core\Entity\HXL\HXLMetadata([
            'license_id' => $this->hxlLicenseId,
            'organisation_id' => "id-of-ushahidi",
            'organisation_name' => "ushahidi",
            'dataset_title' => "ushahidi-dataset",
            'source' => "other",
            'private' => true,
            'user_id' => $this->userId,
            'maintainer_id' =>  'maintainer-1234',
        ]));

        $this->hxlMetaDataId_2 = service('repository.hxl_meta_data')->create(new \Ushahidi\Core\Entity\HXL\HXLMetadata([
            'license_id' => $this->hxlLicenseId,
            'organisation_id' => "id-of-ushahidi",
            'organisation_name' => "ushahidi",
            'dataset_title' => "ushahidi-dataset",
            'source' => "other",
            'private' => true,
            'user_id' => $this->userId,
            'maintainer_id' =>  'maintainer-1234',
        ]));

        $exportJobs = service('repository.export_job');
        $this->jobId = $exportJobs->create(new \Ushahidi\Core\Entity\ExportJob([
            'user_id' => $this->userId,
            'entity_type' => 'post',
            'send_to_hdx' => false,
            'send_to_browser' => true,
            'include_hxl' => false,
            'hxl_meta_data_id' => $this->hxlMetaDataId_1,
        ]));
    }

    public function tearDown()
    {
        parent::tearDown();
        service('repository.hxl_license')->delete(
            new \Ushahidi\Core\Entity\HXL\HXLLicense(['id' => $this->hxlLicenseId])
        );
        service('repository.hxl_meta_data')->delete(
            new \Ushahidi\Core\Entity\HXL\HXLMetadata(['id' => $this->hxlMetaDataId_1])
        );
        service('repository.hxl_meta_data')->delete(
            new \Ushahidi\Core\Entity\HXL\HXLMetadata(['id' => $this->hxlMetaDataId_2])
        );
        service('repository.user')->delete(new \Ushahidi\Core\Entity\User(['id' => $this->userId]));
        service('repository.export_job')->delete(new \Ushahidi\Core\Entity\ExportJob(['id' => $this->jobId]));
    }

    /**
     * Get count
     */
    public function testCount()
    {
        $this->setUserAndJob('admin');
        $this->get('/api/v3/exports/external/count/' . $this->jobId);

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
        $this->setUserAndJob('admin');
        $this->get('/api/v3/exports/external/cli/' . $this->jobId);

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
        $this->setUserAndJob('admin');
        $this->get('/api/v3/exports/external/jobs');

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
        $this->setUserAndJob('admin');
        $this->get('/api/v3/exports/external/jobs/' . $this->jobId);

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
        $this->setUserAndJob('admin');
        $this->json('PUT', '/api/v3/exports/external/jobs/' . $this->jobId, [
            'filters' => ['status' => 'draft'],
            'send_to_hdx' => false,
            'send_to_browser' => true,
            'include_hxl' => false,
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

    protected function makeSig($sharedSecret, $url, $payload)
    {
        $data = $url . $payload;

        return base64_encode(hash_hmac("sha256", $data, $sharedSecret, true));
    }

    /**
     * Update a job
     */
    public function testUpdateJobWithSignature()
    {
        $this->setUserAndJob('admin');
        // Re-enable middleware
        $this->app->instance('middleware.disable', false);
        // Set the shared secret
        $originalSecret = getenv('PLATFORM_SHARED_SECRET');
        putenv('PLATFORM_SHARED_SECRET=asharedsecret');

        // Make an API key
        $apiKeys = service('repository.apikey');
        $apiKeyId = $apiKeys->create(new \Ushahidi\Core\Entity\ApiKey([]));
        $apiKey = $apiKeys->get($apiKeyId);

        // Make a signature
        $sig = $this->makeSig(
            'asharedsecret',
            $this->prepareUrlForRequest(
                '/api/v3/exports/external/jobs/' . $this->jobId . '?api_key=' . $apiKey->api_key
            ),
            json_encode([
                'filters' => ['status' => 'draft'],
                'send_to_hdx' => false,
                'send_to_browser' => true,
                'include_hxl' => false,
                'hxl_meta_data_id' => $this->hxlMetaDataId_2,
            ])
        );

        $this->json(
            'PUT',
            '/api/v3/exports/external/jobs/' . $this->jobId . '?api_key=' . $apiKey->api_key,
            [
                'filters' => ['status' => 'draft'],
                'send_to_hdx' => false,
                'send_to_browser' => true,
                'include_hxl' => false,
                'hxl_meta_data_id' => $this->hxlMetaDataId_2,
            ],
            [
                'X-Ushahidi-Signature' => $sig
            ]
        );

        $this->seeStatusCode('200')
            ->seeJsonStructure([
                'id',
                'user',
                'entity_type',
                'fields',
                'filters',
                'status',
                'header_row',
                'created',
                'hxl_meta_data_id'
            ])
            ->seeJson([
                'filters' => [
                    'status' => 'draft',
                ],
            ]);

        // Clean up
        $apiKeys->delete($apiKey);
        putenv('PLATFORM_SHARED_SECRET=' . $originalSecret);
    }


    /**
     * Test that if we send a regular user without permissions to export,
     * we get a 401
     */
    public function testJobUserIsAuthorized()
    {
        $this->setUserAndJob('user');
        $this->get('/api/v3/exports/external/cli/' . $this->jobId);

        $this->seeStatusCode('401');
    }
}
