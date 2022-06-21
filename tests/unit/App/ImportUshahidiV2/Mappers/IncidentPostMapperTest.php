<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Mappers\IncidentPostMapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportDataTools;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Tests\Unit\App\ImportUshahidiV2\ImportMock;
use Tests\TestCase;
use Mockery as M;
use Faker;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class PostMapperTest extends TestCase
{
    public function testMap()
    {
        $importId = 1;
        $import = ImportMock::forId($importId);
        ImportMock::mockImportTimezone($import, 'UTC');
        $faker = Faker\Factory::create();
        $input = [
            'incident_title' => $faker->sentence(3),
            'incident_description' => $faker->paragraph,
            'form_id' => 30,
            'user_id' => 77,
            'incident_active' => 1,
            'person_email' => $faker->email,
            'person_first' => $faker->firstName,
            'person_last' => $faker->lastName,
            'incident_date' => $faker->date . ' ' . $faker->time,
            'location_name' => $faker->address,
            'latitude' => $faker->latitude,
            'longitude' => $faker->longitude,
            'categories' => '1,4,5',
            'incident_verified' => 1,
            'media' => [],
            'form_responses' => [],
        ];

        $mappingRepo = M::mock(ImportMappingRepository::class);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'form', 30)
            ->andReturn(3);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'user', 77)
            ->andReturn(7);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-location_name')
            ->andReturn(1);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-location')
            ->andReturn(2);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-verified')
            ->andReturn(3);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-categories')
            ->andReturn(4);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-news_source_link')
            ->andReturn(5);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-video_link')
            ->andReturn(6);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-photos')
            ->andReturn(7);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-geometry')
            ->andReturn(null);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'category', '1')
            ->andReturn(11);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'category', '4')
            ->andReturn(44);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'category', '5')
            ->andReturn(55);
        $mappingRepo->shouldReceive('getMetadata')
            ->with($importId, M::any(), M::any())
            ->andReturn(null);

        $attrRepo = M::mock(FormAttributeRepository::class);
        $attrRepo->shouldReceive('get')
            ->with(1)
            ->andReturn(new FormAttribute(['key' => 'location-name-key']));
        $attrRepo->shouldReceive('get')
            ->with(2)
            ->andReturn(new FormAttribute(['key' => 'location-key']));
        $attrRepo->shouldReceive('get')
            ->with(3)
            ->andReturn(new FormAttribute(['key' => 'verified-key']));
        $attrRepo->shouldReceive('get')
            ->with(4)
            ->andReturn(new FormAttribute(['key' => 'categories-key']));
        $attrRepo->shouldReceive('get')
            ->with(5)
            ->andReturn(new FormAttribute(['key' => 'news-key']));
        $attrRepo->shouldReceive('get')
            ->with(6)
            ->andReturn(new FormAttribute(['key' => 'videos-key']));
        $attrRepo->shouldReceive('get')
            ->with(7)
            ->andReturn(new FormAttribute(['key' => 'photos-key']));

        $dataTools = M::mock(ImportDataTools::class);

        $mapper = new IncidentPostMapper($mappingRepo, $attrRepo, $dataTools);
        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $post = $result['result'];

        $this->assertInstanceOf(Post::class, $post);
        $this->assertInstanceOf(\DateTime::class, $post->post_date);
        $this->assertEquals($input['incident_title'], $post->title);
        $this->assertEquals($input['incident_description'], $post->content);
        $this->assertEquals(3, $post->form_id);
        $this->assertEquals(0, $post->parent_id);
        $this->assertEquals('published', $post->status);
        $this->assertEquals($input['person_first'].' '.$input['person_last'], $post->author_realname);
        $this->assertEquals($input['person_email'], $post->author_email);
        $this->assertEquals([11, 44, 55], $post->values['categories-key']);
        $this->assertEquals([$input['location_name']], $post->values['location-name-key']);
        $this->assertEquals(
            [['lat' => $input['latitude'], 'lon' => $input['longitude']]],
            $post->values['location-key']
        );
        $this->assertEquals([1], $post->values['verified-key']);
    }

    public function testMapWithMedia()
    {
        $importId = 1;
        $import = ImportMock::forId($importId);
        ImportMock::mockImportTimezone($import, 'UTC');
        $faker = Faker\Factory::create();
        $input = [
            'incident_title' => $faker->sentence(3),
            'incident_description' => $faker->paragraph,
            'form_id' => 30,
            'user_id' => 77,
            'incident_active' => 1,
            'person_email' => $faker->email,
            'person_first' => $faker->firstName,
            'person_last' => $faker->lastName,
            'incident_date' => $faker->date . ' ' . $faker->time,
            'location_name' => $faker->address,
            'latitude' => $faker->latitude,
            'longitude' => $faker->longitude,
            'categories' => '1,4,5',
            'incident_verified' => 1,
            'media' => [
                (object)[
                    'media_type' => 2,
                    'media_title' => null,
                    'media_link' => 'http://youtube.com/watch?v=some-vidId'
                ],
                (object)[
                    'media_type' => 1,
                    'media_title' => 'Some caption',
                    'media_link' => 'http://something.com/something.png'
                ],
                (object)[
                    'media_type' => 4,
                    'media_title' => null,
                    'media_link' => 'http://news.com/something'
                ],
                (object)[
                    'media_type' => 4,
                    'media_title' => null,
                    'media_link' => 'http://junk.com/something'
                ]
            ],
            'form_responses' => [],
        ];

        $mappingRepo = M::mock(ImportMappingRepository::class);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'form', 30)
            ->andReturn(3);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'user', 77)
            ->andReturn(7);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-location_name')
            ->andReturn(1);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-location')
            ->andReturn(2);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-verified')
            ->andReturn(3);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-categories')
            ->andReturn(4);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-news_source_link')
            ->andReturn(5);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-video_link')
            ->andReturn(6);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-photos')
            ->andReturn(7);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-geometry')
            ->andReturn(null);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'category', '1')
            ->andReturn(11);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'category', '4')
            ->andReturn(44);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'category', '5')
            ->andReturn(55);
        $mappingRepo->shouldReceive('getMetadata')
            ->with($importId, M::any(), M::any())
            ->andReturn(null);

        $attrRepo = M::mock(FormAttributeRepository::class);
        $attrRepo->shouldReceive('get')
            ->with(1)
            ->andReturn(new FormAttribute(['key' => 'location-name-key']));
        $attrRepo->shouldReceive('get')
            ->with(2)
            ->andReturn(new FormAttribute(['key' => 'location-key']));
        $attrRepo->shouldReceive('get')
            ->with(3)
            ->andReturn(new FormAttribute(['key' => 'verified-key']));
        $attrRepo->shouldReceive('get')
            ->with(4)
            ->andReturn(new FormAttribute(['key' => 'categories-key']));
        $attrRepo->shouldReceive('get')
            ->with(5)
            ->andReturn(new FormAttribute(['key' => 'news-key']));
        $attrRepo->shouldReceive('get')
            ->with(6)
            ->andReturn(new FormAttribute(['key' => 'videos-key']));
        $attrRepo->shouldReceive('get')
            ->with(7)
            ->andReturn(new FormAttribute(['key' => 'photos-key']));

        $dataTools = M::mock(ImportDataTools::class);

        $mapper = new IncidentPostMapper($mappingRepo, $attrRepo, $dataTools);
        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $post = $result['result'];

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals(
            [
                [
                    'o_filename' => 'http://something.com/something.png',
                    'caption' => 'Some caption',
                    'mime' => 'image/png',
                    'user_id' => 7
                ],
            ],
            $post->values['photos-key']
        );
        $this->assertEquals(
            [
                'https://www.youtube.com/embed/some-vidId',
            ],
            $post->values['videos-key']
        );
        $this->assertEquals(
            [
                'http://news.com/something',
                'http://junk.com/something',
            ],
            $post->values['news-key']
        );
    }

    public function testMapWithFormResponses()
    {
        $importId = 1;
        $import = ImportMock::forId($importId);
        ImportMock::mockImportTimezone($import, 'UTC');
        $faker = Faker\Factory::create();
        $input = [
            'incident_title' => $faker->sentence(3),
            'incident_description' => $faker->paragraph,
            'form_id' => 30,
            'user_id' => 77,
            'incident_active' => 1,
            'person_email' => $faker->email,
            'person_first' => $faker->firstName,
            'person_last' => $faker->lastName,
            'incident_date' => $faker->date . ' ' . $faker->time,
            'location_name' => $faker->address,
            'latitude' => $faker->latitude,
            'longitude' => $faker->longitude,
            'categories' => '1,4,5',
            'incident_verified' => 1,
            'media' => [],
            'form_responses' => [
                (object)[
                    'id' => 1,
                    'form_field_id' => 6,
                    'form_response' => 'Something',
                    'field_type' => 1,
                    'field_datatype' => 'text',
                    'field_isdate' => 0
                ],
                (object)[
                    'id' => 2,
                    'form_field_id' => 7,
                    'form_response' => 'Again',
                    'field_type' => 1,
                    'field_datatype' => 'text',
                    'field_isdate' => 0
                ],
                (object)[
                    'id' => 3,
                    'form_field_id' => 8,
                    'form_response' => 'Things',
                    'field_type' => 1,
                    'field_datatype' => 'text',
                    'field_isdate' => 0
                ],
                (object)[
                    'id' => 4,
                    'form_field_id' => 8,
                    'form_response' => 'Things2',
                    'field_type' => 1,
                    'field_datatype' => 'text',
                    'field_isdate' => 0
                ]
            ],
        ];

        $mappingRepo = M::mock(ImportMappingRepository::class);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'form', 30)
            ->andReturn(3);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'user', 77)
            ->andReturn(7);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-location_name')
            ->andReturn(1);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-location')
            ->andReturn(2);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-verified')
            ->andReturn(3);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-categories')
            ->andReturn(4);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-news_source_link')
            ->andReturn(5);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-video_link')
            ->andReturn(6);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-photos')
            ->andReturn(7);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident_column', '30-geometry')
            ->andReturn(null);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'category', '1')
            ->andReturn(11);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'category', '4')
            ->andReturn(44);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'category', '5')
            ->andReturn(55);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'form_field', 6)
            ->andReturn(66);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'form_field', 7)
            ->andReturn(77);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'form_field', 8)
            ->andReturn(88);
        $mappingRepo->shouldReceive('getMetadata')
            ->with($importId, M::any(), M::any())
            ->andReturn(null);

        $attrRepo = M::mock(FormAttributeRepository::class);
        $attrRepo->shouldReceive('get')
            ->with(1)
            ->andReturn(new FormAttribute([
                'key' => 'location-name-key',
                'input' => 'text',
                'type' => 'varchar'
            ]));
        $attrRepo->shouldReceive('get')
            ->with(2)
            ->andReturn(new FormAttribute([
                'key' => 'location-key',
                'input' => 'location',
                'type' => 'point'
            ]));
        $attrRepo->shouldReceive('get')
            ->with(3)
            ->andReturn(new FormAttribute([
                'key' => 'verified-key',
                'input' => 'checkbox',
                'type' => 'int'
            ]));
        $attrRepo->shouldReceive('get')
            ->with(4)
            ->andReturn(new FormAttribute([
                'key' => 'categories-key',
                'input' => 'tags',
                'type' => 'tags'
            ]));
        $attrRepo->shouldReceive('get')
            ->with(5)
            ->andReturn(new FormAttribute([
                'key' => 'news-key',
                'input' => 'text',
                'type' => 'varchar'
            ]));
        $attrRepo->shouldReceive('get')
            ->with(6)
            ->andReturn(new FormAttribute([
                'key' => 'videos-key',
                'input' => 'video',
                'type' => 'varchar'
            ]));
        $attrRepo->shouldReceive('get')
            ->with(7)
            ->andReturn(new FormAttribute([
                'key' => 'photos-key',
                'input' => 'upload',
                'type' => 'media'
            ]));
        $attrRepo->shouldReceive('get')
            ->with(66)
            ->andReturn(new FormAttribute([
                'key' => 'custom6-key',
                'input' => 'text',
                'type' => 'varchar'
            ]));
        $attrRepo->shouldReceive('get')
            ->with(77)
            ->andReturn(new FormAttribute([
                'key' => 'custom7-key',
                'input' => 'text',
                'type' => 'varchar'
            ]));
        $attrRepo->shouldReceive('get')
            ->with(88)
            ->andReturn(new FormAttribute([
                'key' => 'custom8-key',
                'input' => 'text',
                'type' => 'varchar'
            ]));

        $dataTools = M::mock(ImportDataTools::class);

        $mapper = new IncidentPostMapper($mappingRepo, $attrRepo, $dataTools);
        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $post = $result['result'];

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals(
            [
                'Something'
            ],
            $post->values['custom6-key']
        );
        $this->assertEquals(
            [
                'Again',
            ],
            $post->values['custom7-key']
        );
        $this->assertEquals(
            [
                'Things',
                'Things2',
            ],
            $post->values['custom8-key']
        );
    }
}
