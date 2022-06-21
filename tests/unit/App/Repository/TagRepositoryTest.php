<?php

namespace Tests\Unit\App\Repository;

use Ushahidi\App\Repository\TagRepository;
use Ushahidi\Core\Entity\Tag;
use Tests\TestCase;
use Mockery as M;
use Faker;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class TagRepositoryTest extends TestCase
{
    public function testCreateMany()
    {
        $faker = Faker\Factory::create();

        // Generate tag data
        $tag1 = new Tag([
            'tag' => $faker->word,
            'description' => $faker->sentence,
        ]);
        $tag2 = new Tag([
            'tag' => $faker->word,
            'description' => $faker->sentence,
        ]);
        $tag3 = new Tag([
            'tag' => $faker->word,
            'description' => $faker->sentence,
        ]);

        $repo = service('repository.tag');
        $inserted = $repo->createMany(collect([
            $tag1,
            $tag2,
            $tag3,
        ]));

        $this->assertCount(3, $inserted);
        $this->seeInDatabase('tags', [
            'id' => $inserted[0],
            'tag' => $tag1->tag,
            'description' => $tag1->description
        ]);
        $this->seeInDatabase('tags', [
            'id' => $inserted[1],
            'tag' => $tag2->tag,
            'description' => $tag2->description
        ]);
        $this->seeInDatabase('tags', [
            'id' => $inserted[2],
            'tag' => $tag3->tag,
            'description' => $tag3->description
        ]);
    }
}
