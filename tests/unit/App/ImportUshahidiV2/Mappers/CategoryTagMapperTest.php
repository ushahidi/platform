<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Mappers\CategoryTagMapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\Core\Entity\Tag;
use Tests\Unit\App\ImportUshahidiV2\ImportMock;
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
            'category_visible' => 1
        ];

        $repo = M::mock(ImportMappingRepository::class);
        $repo->shouldReceive('getDestId')
            ->with(1, 'category', 0)
            ->andReturn(null);

        $mapper = new CategoryTagMapper($repo);
        $import = ImportMock::forId(1);
        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $tag = $result['result'];

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
            'category_visible' => 1,
            'parent_id' => 10
        ];

        $repo = M::mock(ImportMappingRepository::class);
        $repo->shouldReceive('getDestId')
            ->with(1, 'category', 10)
            ->andReturn(110);

        $mapper = new CategoryTagMapper($repo);
        $import = ImportMock::forId(1);
        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $tag = $result['result'];

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals($input['category_title'], $tag->tag);
        $this->assertEquals($input['category_description'], $tag->description);
        $this->assertEquals('b0a50d', $tag->color);
        $this->assertEquals(110, $tag->parent_id);
    }

    public function testMapVisiblity()
    {
        $faker = Faker\Factory::create();

        $repo = M::mock(ImportMappingRepository::class);
        $repo->shouldReceive('getDestId')
            ->with(1, 'category', 0)
            ->andReturn(null);

        $mapper = new CategoryTagMapper($repo);

        $input = [
            'category_title' => $faker->word,
            'category_description' => $faker->sentence,
            'category_color' => '#3af364',
            'category_visible' => 0,
            'parent_id' => 0
        ];

        $import = ImportMock::forId(1);
        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $tag = $result['result'];

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals(['admin'], $tag->role);

        $input = [
            'category_title' => $faker->word,
            'category_description' => $faker->sentence,
            'category_color' => '#3af364',
            'category_visible' => 1,
            'parent_id' => 0
        ];

        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $tag = $result['result'];
        
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals([], $tag->role);
    }
}
