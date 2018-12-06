<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Mappers\CategoryTagMapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\Core\Entity\Tag;
use Tests\TestCase;
use Mockery as M;
use Faker;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class TagMapperTest extends TestCase
{
    public function testMap()
    {
        $faker = Faker\Factory::create();
        $input = [
            'category_title' => $faker->word,
            'category_description' => $faker->sentence,
            'category_color' => '#3af364',
            'parent_id' => 0
        ];

        $repo = M::mock(ImportMappingRepository::class);
        $repo->shouldReceive('getDestId')
            ->with('category', 0)
            ->andReturn(null);

        $mapper = new CategoryTagMapper($repo);

        $tag = $mapper($input);

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals($input['category_title'], $tag->tag);
        $this->assertEquals($input['category_description'], $tag->description);
        $this->assertEquals('3af364', $tag->color);
        $this->assertEquals(0, $tag->parent_id);
    }

    public function testMapWithParent()
    {
        $faker = Faker\Factory::create();
        $input = [
            'category_title' => $faker->word,
            'category_description' => $faker->sentence,
            'category_color' => '#b0a50d',
            'parent_id' => 10
        ];

        $repo = M::mock(ImportMappingRepository::class);
        $repo->shouldReceive('getDestId')
            ->with('category', 10)
            ->andReturn(110);

        $mapper = new CategoryTagMapper($repo);

        $tag = $mapper($input);

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals($input['category_title'], $tag->tag);
        $this->assertEquals($input['category_description'], $tag->description);
        $this->assertEquals('b0a50d', $tag->color);
        $this->assertEquals(110, $tag->parent_id);
    }
}
