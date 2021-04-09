<?php

namespace Tests\Unit\App\Repository;

use Ushahidi\App\Repository\PostRepository;
use Ushahidi\Core\Entity\Post;
use Tests\TestCase;
use Tests\DatabaseTransactions;
use Mockery as M;
use Faker;

use Ohanzee\DB;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class PostRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        // Add some test attributes
        DB::insert('form_attributes')
            ->columns(['key', 'type', 'input', 'label'])
            ->values(['test-location', 'point', 'location', 'location'])
            ->values(['test-tags', 'tags', 'tags', 'categories'])
            ->values(['test-test', 'varchar', 'text', 'some text'])
            ->values(['test-media', 'media', 'upload', 'photos'])
            ->execute($this->database);

        DB::insert('tags')
            ->columns(['tag', 'slug', 'id'])
            ->values(['test-tag1', 'test-tag1', 99991])
            ->values(['test-tag2', 'test-tag2', 99992])
            ->values(['test-tag3-test', 'test-tag3', 99993])
            ->execute($this->database);

        DB::insert('media')
            ->columns(['o_filename', 'caption', 'id', 'mime', 'o_size'])
            ->values(['junk.png', 'Junk', 8889, 'image/png', 0])
            ->execute($this->database);
    }

    public function testCreateMany()
    {
        $faker = Faker\Factory::create();

        // Generate post data
        $post1 = new Post([
            'title' => $faker->sentence,
            'context' => $faker->paragraph,
            'type' => 'report',
            'status' => 'published',
            'locale' => 'en_US',
            'values' => [
                'test-location' => [[
                    'lat' => 1,
                    'lon' => 2
                ]],
                'test-tags' => [99991,99993],
                'test-test' => ['text'],
            ]
        ]);
        $post2 = new Post([
            'title' => $faker->sentence,
            'context' => $faker->paragraph,
            'type' => 'report',
            'status' => 'published',
            'locale' => 'en_US',
            'values' => [
                'test-location' => [[
                    'lat' => 2,
                    'lon' => 4
                ]],
                'test-tags' => [99992],
                'test-test' => ['text2'],
            ]
        ]);
        $post3 = new Post([
            'title' => $faker->sentence,
            'context' => $faker->paragraph,
            'type' => 'report',
            'status' => 'published',
            'locale' => 'en_US',
            'values' => [
                'test-location' => [[
                    'lat' => 7,
                    'lon' => 9
                ]],
                'test-tags' => [99993],
                'test-test' => ['text3'],
            ]
        ]);

        $repo = service('repository.post');
        $inserted = $repo->createMany(collect([
            $post1,
            $post2,
            $post3,
        ]));

        $this->assertCount(3, $inserted);
        $this->seeInOhanzeeDatabase('posts', [
            'id' => $inserted[0],
            'title' => $post1->title,
        ]);
        $this->seeInOhanzeeDatabase('posts', [
            'id' => $inserted[1],
            'title' => $post2->title,
        ]);
        $this->seeInOhanzeeDatabase('posts', [
            'id' => $inserted[2],
            'title' => $post3->title,
        ]);

        $this->seeInOhanzeeDatabase('post_point', [
            'post_id' => $inserted[0],
            'value' => DB::expr('POINT(2, 1)')
        ]);
        $this->seeInOhanzeeDatabase('post_point', [
            'post_id' => $inserted[1],
            'value' => DB::expr('POINT(4, 2)')
        ]);
        $this->seeInOhanzeeDatabase('post_point', [
            'post_id' => $inserted[2],
            'value' => DB::expr('POINT(9, 7)')
        ]);

        $this->seeInOhanzeeDatabase('posts_tags', [
            'post_id' => $inserted[0],
            'tag_id' => 99991
        ]);
        $this->seeInOhanzeeDatabase('posts_tags', [
            'post_id' => $inserted[0],
            'tag_id' => 99993
        ]);
        $this->seeInOhanzeeDatabase('posts_tags', [
            'post_id' => $inserted[1],
            'tag_id' => 99992
        ]);
        $this->seeInOhanzeeDatabase('posts_tags', [
            'post_id' => $inserted[2],
            'tag_id' => 99993
        ]);

        $this->seeInOhanzeeDatabase('post_varchar', [
            'post_id' => $inserted[0],
            'value' => 'text'
        ]);
        $this->seeInOhanzeeDatabase('post_varchar', [
            'post_id' => $inserted[1],
            'value' => 'text2'
        ]);
        $this->seeInOhanzeeDatabase('post_varchar', [
            'post_id' => $inserted[2],
            'value' => 'text3'
        ]);
    }

    public function testCreateManyWithMedia()
    {
        $faker = Faker\Factory::create();

        // Generate post data
        $post1 = new Post([
            'title' => $faker->sentence,
            'context' => $faker->paragraph,
            'type' => 'report',
            'status' => 'published',
            'locale' => 'en_US',
            'values' => [
                'test-location' => [[
                    'lat' => 1,
                    'lon' => 2
                ]],
                'test-tags' => [99991,99993],
                'test-test' => ['text'],
                'test-media' => [
                    [
                        'o_filename' => 'somefile.png',
                        'caption' => 'a title',
                        'mime' => 'image/png',
                    ]
                ]
            ]
        ]);
        $post2 = new Post([
            'title' => $faker->sentence,
            'context' => $faker->paragraph,
            'type' => 'report',
            'status' => 'published',
            'locale' => 'en_US',
            'values' => [
                'test-location' => [[
                    'lat' => 2,
                    'lon' => 4
                ]],
                'test-tags' => [99992],
                'test-test' => ['text2'],
                'test-media' => [
                    [
                        'o_filename' => 'somefile2.png',
                        'caption' => 'a title',
                        'mime' => 'image/png',
                    ],
                    8889
                ]
            ]
        ]);

        $repo = service('repository.post');
        $inserted = $repo->createMany(collect([
            $post1,
            $post2
        ]));

        $this->assertCount(2, $inserted);
        $this->seeInOhanzeeDatabase('posts', [
            'id' => $inserted[0],
            'title' => $post1->title,
        ]);
        $this->seeInOhanzeeDatabase('posts', [
            'id' => $inserted[1],
            'title' => $post2->title,
        ]);

        $this->seeCountInOhanzeeDatabase('post_media', [
            'post_id' => $inserted[0],
        ], 1);
        $this->seeCountInOhanzeeDatabase('post_media', [
            'post_id' => $inserted[1],
        ], 2);
        $this->seeInOhanzeeDatabase('post_media', [
            'post_id' => $inserted[1],
            'value' => 8889
        ]);
    }

    public function testSetSearchParamsLimitedUnprivileged()
    {
        $this->doTestSetSearchParams(0, 1, config('posts.list_max_limit'));
    }

    public function testSetSearchParamsLimitedPrivileged()
    {
        $this->doTestSetSearchParams(1, 1, config('posts.list_admin_max_limit'));
    }

    public function testSetSearchParamsNonlimitedUnprivileged()
    {
        $this->doTestSetSearchParams(0, 0);
    }

    public function testSetSearchParamsNonlimitedPrivileged()
    {
        $this->doTestSetSearchParams(1, 0);
    }

    /**
     * bool $canManagePosts
     * bool $limitPosts
     * int|null $expectedLimit
     */
    public function doTestSetSearchParams($canManagePosts, $limitPosts, $expectedLimit = null)
    {
        // we don't need to test anything but the LIMIT on the end of the sql so mock everything else in the db
        $db = M::mock(\Ohanzee\Database::class);
        $db->shouldReceive('quote_column');
        $db->shouldReceive('quote_table');
        $db->shouldReceive('quote');
        $resolver = M::mock(\Ushahidi\App\Multisite\OhanzeeResolver::class);
        $resolver->shouldReceive('connection')->andReturn($db);
        $form_attribute_repo = M::mock(\Ushahidi\Core\Entity\FormAttributeRepository::class);
        $form_stage_repo = M::mock(\Ushahidi\Core\Entity\FormStageRepository::class);
        $attrs = M::mock(\Illuminate\Support\Collection::class);
        $attrs->shouldReceive('groupBy');
        $attrs->shouldReceive('keyBy');
        $form_repo = M::mock(\Ushahidi\Core\Entity\FormRepository::class);
        $form_repo->shouldReceive('getAllFormStagesAttributes')->andReturn($attrs);
        $post_lock_repo = M::mock(\Ushahidi\Core\Entity\PostLockRepository::class);
        $contact_repo = M::mock(\Ushahidi\Core\Entity\ContactRepository::class);
        $post_value_factory = M::mock(\Ushahidi\App\Repository\Post\ValueFactory::class);
        $bounding_box_factory = M::mock(\Aura\Di\Injection\Factory::class);

        $repo = new PostRepository(
            $resolver,
            $form_attribute_repo,
            $form_stage_repo,
            $form_repo,
            $post_lock_repo,
            $contact_repo,
            $post_value_factory,
            $bounding_box_factory
        );
        $user = new \Ushahidi\Core\Entity\User();
        $session = M::mock(\Ushahidi\Core\Session::class);
        $session->shouldReceive('getUser')->andReturn($user);
        $repo->setSession($session);

        $postPermissions = M::mock(\Ushahidi\Core\Tool\Permissions\PostPermissions::class);
        $postPermissions->shouldReceive('canUserManagePosts')->with($user)
            ->andReturn($canManagePosts)->times($limitPosts);
        $repo->setPostPermissions($postPermissions);

        $search = M::Mock(\Ushahidi\Core\SearchData::class);
        $fakeLimit = 10000; // this limit should be overridden if limitPosts
        $search->shouldReceive('getSorting')->andReturn(['limit' => $fakeLimit]);
        $search->shouldReceive('getFilter')->with('limitPosts')->andReturn($limitPosts)->once();
        $search->shouldReceive('getFilter'); // we only care about limitPosts
        // run the test method
        $query = $repo->setSearchParams($search);
        // grab the resulting SQL and pull off the LIMIT clause on the end
        $sql = $query->compile($db);
        $limitPos = strpos($sql, 'LIMIT ');
        $limit = substr($sql, $limitPos+6);
        $expectedLimit = $expectedLimit ?? $fakeLimit;
        $this->assertEquals($expectedLimit, $limit);
    }
}
