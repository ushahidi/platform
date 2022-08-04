<?php
namespace Ushahidi\Tests\Feature\V3;

use Faker;
use Ushahidi\Tests\TestCase;
use Ushahidi\Core\Entity\ApiKey;
use Ushahidi\Core\Entity\Post;

/**
 * @group api
 * @group integration
 */
class WebhooksPostsUpdateAPI extends TestCase
{
    protected $postId;

    /**
     * Moved this out of setup.
     * Helper to set the user role and the job correctly since we
     * override this setup in the test for authorization
     * @param $user_role
     */
    public function setUp()
    {
        parent::setUp();

        $faker = Faker\Factory::create();

        $this->postId = service('repository.post')->create(new Post([
            'title' => $faker->word,
            'content' => $faker->text,
        ]));
    }

    public function tearDown()
    {
        service('repository.post')->delete(new Post(['id' => $this->postId]));

        parent::tearDown();
    }

    protected function makeSig($sharedSecret, $url, $payload)
    {
        $data = $url.$payload;

        return base64_encode(hash_hmac('sha256', $data, $sharedSecret, true));
    }

    /**
     * Get count
     */
    public function testUpdate()
    {
        $this->withoutMiddleware();
        $this->json('PUT', '/api/v3/webhooks/posts/'.$this->postId, [
            'title' => 'Updated',
            'content' => 'Also updated',
        ]);

        $this->seeStatusCode('200')
            ->seeJson([
                'title' => 'Updated',
                'content' => 'Also updated',
            ]);
    }

    /**
     * Update a job
     */
    public function testUpdateWithSignature()
    {
        // Re-enable middleware
        $this->app->instance('middleware.disable', false);
        // Set the shared secret
        $originalSecret = getenv('PLATFORM_SHARED_SECRET');
        putenv('PLATFORM_SHARED_SECRET=asharedsecret');

        // Make an API key
        $apiKeys = service('repository.apikey');
        $apiKeyId = $apiKeys->create(new ApiKey([]));
        $apiKey = $apiKeys->get($apiKeyId);

        // Make a signature
        $sig = $this->makeSig(
            'asharedsecret',
            $this->prepareUrlForRequest(
                '/api/v3/webhooks/posts/'.$this->postId.'?api_key='.$apiKey->api_key
            ),
            json_encode([
                'title' => 'Updated w/sig',
                'content' => 'Also updated',
            ])
        );

        $this->json(
            'PUT',
            '/api/v3/webhooks/posts/'.$this->postId.'?api_key='.$apiKey->api_key,
            [
                'title' => 'Updated w/sig',
                'content' => 'Also updated',
            ],
            [
                'X-Ushahidi-Signature' => $sig,
            ]
        );

        $this->seeStatusCode('200')
            ->seeJsonStructure([
                'id',
                'user_id',
                'type',
                'title',
                'content',
                'status',
                'values',
                'created',
                'updated',
            ])
            ->seeJson([
                'title' => 'Updated w/sig',
                'content' => 'Also updated',
            ]);

        // Clean up
        $apiKeys->delete($apiKey);
        putenv('PLATFORM_SHARED_SECRET='.$originalSecret);
    }
}
