<?php

namespace Ushahidi\Tests\Feature\V3;

use Faker;
use Illuminate\Support\Facades\Bus;
use Ushahidi\Authzn\GenericUser;
use Ushahidi\Core\Entity\User;
use Ushahidi\Modules\V3\Jobs\ExportPostsJob;
use Ushahidi\Tests\TestCase;

/**
 * @group api
 * @group integration
 */
class ExportJobAPITest extends TestCase
{
    private const REQUEST = [
        'fields' => 'test',
        'filters' => [
            'status' => ['published', 'draft'],
        ],
        'entity_type' => 'post',
        'send_to_browser' => true,
        'send_to_hdx' => false,
        'hxl_heading_row' => ['something'],
    ];

    private const EXPECTED = [
        'fields' => ['test'],
        'filters' => [
            'status' => ['published', 'draft'],
        ],
        'entity_type' => 'post',
        'send_to_browser' => true,
        'send_to_hdx' => false,
        'hxl_heading_row' => null,
    ];

    /**
     * Creating an export job requires admin rights. The test owns its admin
     * user rather than relying on a fixture: the behat dataset is not loaded
     * for phpunit runs, and it rewrites the users table when it is.
     */
    protected $adminId;

    public function setUp(): void
    {
        parent::setUp();

        $faker = Faker\Factory::create();

        $this->adminId = service('repository.user')->create(new User([
            'email' => $faker->unique()->safeEmail,
            'realname' => 'Export Admin',
            'role' => 'admin',
        ]));
    }

    public function tearDown(): void
    {
        service('repository.user')->delete(new User(['id' => $this->adminId]));

        parent::tearDown();
    }

    private function postJob()
    {
        return $this->actingAs(new GenericUser(['id' => $this->adminId]))
            ->json('POST', '/api/v3/exports/jobs', self::REQUEST);
    }

    /**
     * Create a job, asserting the export is queued rather than run inline.
     */
    public function testCreateJob()
    {
        Bus::fake();

        $this->withoutMiddleware();

        $this->postJob()
            ->assertStatus(200)
            ->assertJson(self::EXPECTED);

        Bus::assertDispatched(ExportPostsJob::class);
    }

    /**
     * Create a job and let the export actually dispatch.
     */
    public function testCreateJobWithRealDispatch()
    {
        $this->withoutMiddleware();

        $this->postJob()
            ->assertStatus(200)
            ->assertJson(self::EXPECTED);
    }
}
