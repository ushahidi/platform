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
            ->execute($this->database);

        DB::insert('tags')
            ->columns(['tag', 'slug', 'id'])
            ->values(['test-tag1', 'test-tag1', 99991])
            ->values(['test-tag2', 'test-tag2', 99992])
            ->values(['test-tag3-test', 'test-tag3', 99993])
            ->execute($this->database);
    }

    public function testCreateMany()
    {
        // Do stuff
        // Check data in DB
        // rollback transaction

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
}
