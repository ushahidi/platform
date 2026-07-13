<?php

namespace Ushahidi\Tests\Feature\V3;

use Faker;
use Ushahidi\Core\Entity\ApiKey;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Tests\TestCase;

/**
 * @group api
 * @group integration
 */
class WebhooksPostsUpdateAPITest extends TestCase
{
    protected $postId;

    public function setUp(): void
    {
        parent::setUp();

        $faker = Faker\Factory::create();

        $this->postId = service('repository.post')->create(new Post([
            'title' => $faker->word,
            'content' => $faker->text,
        ]));
    }

    public function tearDown(): void
    {
        service('repository.post')->delete(new Post(['id' => $this->postId]));

        parent::tearDown();
    }

    protected function makeSig($sharedSecret, $url, $payload)
    {
        $data = $url.$payload;

        return base64_encode(hash_hmac('sha256', $data, $sharedSecret, true));
    }

    public function testUpdate()
    {
        $this->withoutMiddleware();

        $this->json('PUT', '/api/v3/webhooks/posts/'.$this->postId, [
            'title' => 'Updated',
            'content' => 'Also updated',
        ])
            ->assertStatus(200)
            ->assertJson([
                'title' => 'Updated',
                'content' => 'Also updated',
            ]);
    }

    /**
     * Middleware is left enabled here: the point of the test is that a request
     * carrying a valid X-Ushahidi-Signature is accepted.
     */
    public function testUpdateWithSignature()
    {
        // Set the shared secret
        $originalSecret = getenv('PLATFORM_SHARED_SECRET');
        putenv('PLATFORM_SHARED_SECRET=asharedsecret');

        // Make an API key
        $apiKeys = service('repository.apikey');
        $apiKeyId = $apiKeys->create(new ApiKey([]));
        $apiKey = $apiKeys->get($apiKeyId);

        $url = '/api/v3/webhooks/posts/'.$this->postId.'?api_key='.$apiKey->api_key;

        $payload = [
            'title' => 'Updated w/sig',
            'content' => 'Also updated',
        ];

        // Make a signature
        $sig = $this->makeSig(
            'asharedsecret',
            $this->prepareUrlForRequest($url),
            json_encode($payload)
        );

        $this->json('PUT', $url, $payload, ['X-Ushahidi-Signature' => $sig])
            ->assertStatus(200)
            ->assertJsonStructure([
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
            ->assertJson($payload);

        // Clean up
        $apiKeys->delete($apiKey);
        putenv('PLATFORM_SHARED_SECRET='.$originalSecret);
    }
}
