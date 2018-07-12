<?php
/**
 * Created by PhpStorm.
 * User: rowasc
 * Date: 5/4/18
 * Time: 1:33 PM
 */

namespace Tests\Unit\App\Formatter\Post;

use Tests\TestCase;
use Tests\Unit\Core\Entity\MockPostEntity;
use Ushahidi\App\Formatter\Post\CSV;

class CSVTest extends TestCase
{
    protected $headerRow;
    protected $formatter;
    protected $fs;

    public function setUp()
    {
        parent::setup();
        $this->fs = \Mockery::mock(\League\Flysystem\Filesystem::class);

        $this->fs->shouldReceive('putStream')->andReturn([]);
        $this->fs->shouldReceive('getSize')->andReturn(200);
        $this->fs->shouldReceive('getMimetype')->andReturn('text/csv');
        $this->formatter = new CSV();
        $this->formatter->setFilesystem($this->fs);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testCSVRowsAreCreated()
    {
        $this->headerRow = json_decode('[
		  {
			"label": "Post ID",
			"key": "id",
			"type": "integer",
			"input": "number",
			"form_id": 0,
			"form_stage_id": 0,
			"form_stage_priority": 0,
			"priority": 1
		  },
		  {
			"label": "Post Status",
			"key": "status",
			"type": "string",
			"input": "string",
			"form_id": 0,
			"form_stage_id": 0,
			"form_stage_priority": 0,
			"priority": 2
		  },
		  {
			"label": "Created (UTC)",
			"key": "created",
			"type": "datetime",
			"input": "native",
			"form_id": 0,
			"form_stage_id": 0,
			"form_stage_priority": 0,
			"priority": 3
		  },
		  {
			"label": "Updated (UTC)",
			"key": "updated",
			"type": "datetime",
			"input": "native",
			"form_id": 0,
			"form_stage_id": 0,
			"form_stage_priority": 0,
			"priority": 4
		  },
		  {
			"label": "Post Date (UTC)",
			"key": "post_date",
			"type": "datetime",
			"input": "native",
			"form_id": 0,
			"form_stage_id": 0,
			"form_stage_priority": 0,
			"priority": 5
		  },
		  {
			"label": "Contact ID",
			"key": "contact_id",
			"type": "integer",
			"input": "number",
			"form_id": 0,
			"form_stage_id": 0,
			"form_stage_priority": 0,
			"priority": 6
		  },
		  {
			"label": "Contact",
			"key": "contact",
			"type": "text",
			"input": "text",
			"form_id": 0,
			"form_stage_id": 0,
			"form_stage_priority": 0,
			"priority": 7
          },
		  {
			"label": "Unstructured Description",
			"key": "description",
			"type": "description",
			"input": "text",
			"form_id": 0,
			"form_stage_id": 0,
			"form_stage_priority": 0,
            "unstructured": "1",
			"priority": 8
		  },
		  {
			"id": "4",
			"key": "descriptionreal",
			"label": "Description",
			"instructions": null,
			"input": "textarea",
			"type": "description",
			"required": "0",
			"default": null,
			"priority": "0",
			"options": "",
			"cardinality": "1",
			"config": null,
			"form_stage_id": "1",
			"response_private": "0",
			"description": null,
			"form_stage_priority": "99",
			"form_id": "1"
		  },
		  {
			"id": "17",
			"key": "title",
			"label": "Title",
			"instructions": null,
			"input": "text",
			"type": "title",
			"required": "0",
			"default": null,
			"priority": "0",
			"options": "",
			"cardinality": "1",
			"config": null,
			"form_stage_id": "1",
			"response_private": "0",
			"description": null,
			"form_stage_priority": "99",
			"form_id": "1"
		  },
		  {
			"id": "25",
			"key": "markdown",
			"label": "Test markdown",
			"instructions": null,
			"input": "text",
			"type": "markdown",
			"required": "0",
			"default": null,
			"priority": "1",
			"options": "",
			"cardinality": "1",
			"config": null,
			"form_stage_id": "1",
			"response_private": "0",
			"description": null,
			"form_stage_priority": "99",
			"form_id": "1"
		  },
		  {
			"id": "26",
			"key": "tags1",
			"label": "Categories",
			"instructions": null,
			"input": "tags",
			"type": "tags",
			"required": "0",
			"default": null,
			"priority": "3",
			"options": "[\"1\",\"2\",\"3\",\"4\",\"5\",\"6\",\"7\"]",
			"cardinality": "0",
			"config": null,
			"form_stage_id": "1",
			"response_private": "0",
			"description": null,
			"form_stage_priority": "99",
			"form_id": "1"
		  },
		  {
			"id": "27",
			"key": "tags2",
			"label": "Categories",
			"instructions": null,
			"input": "tags",
			"type": "tags",
			"required": "0",
			"default": null,
			"priority": "3",
			"options": "[\"1\",\"2\",\"3\",\"5\"]",
			"cardinality": "0",
			"config": null,
			"form_stage_id": "4",
			"response_private": "0",
			"description": null,
			"form_stage_priority": "99",
			"form_id": "2"
		  },
		  {
			  "id": "7",
			  "key": "last_location",
			  "label": "Last Location",
			  "instructions": null,
			  "input": "text",
			  "type": "point",
			  "required": "1",
			  "default": null,
			  "priority": "5",
			  "options": "",
			  "cardinality": "1",
			  "config": null,
			  "form_stage_id": "1",
			  "response_private": "0",
			  "description": null,
			  "form_stage_priority": "99",
			  "form_id": "3"
			}
		]', true);
        $keyAttributes = [];
        foreach ($this->headerRow as $key => $item) {
            $keyAttributes[$item['key']] = $item;
        }

        $posts = [new MockPostEntity(), new MockPostEntity(), new MockPostEntity()];
        $postKeys = ['post_date' => '2017-02-22'];
        $postsFinal = [];
        foreach ($posts as $post) {
            foreach ($postKeys as $key => $value) {
                if ($value) {
                    $post->setStateValue($key, $value);
                } else {
                    $post->setStateValue($key, random_bytes(256));
                }
            }
            $postsFinal[] = $post->asArray();
        }
        $this->formatter->createHeading($this->headerRow);
        $formatter = $this->formatter;
        $values = $formatter->formatRecordForCSV([
            'post_date' => '2017-02-22',
            'created' => '2017-02-23',
            'updated' => '2017-02-24',
            'id' => 1234,
            'title' => 'This title has content',
            'form_id' => 1,
            'content' => 'This is a description',
            'contact' => 123456,
            'status' => 'draft',
            'values' => [
                'tags2' => [1, 2, 3],
                'tags5' => [222],//will not appear in response since there is no attribute matching
                'last_location' => [
                    [
                        'lon' => 8888,
                        'lat' => 9999
                    ],
                    [
                        'lon' => 8888,
                        'lat' => 9999
                    ],
                ]
            ]
        ], $keyAttributes);
        // check that the format matches what is expected from the attribute list
        $this->assertEquals([
            1234,
            'draft',
            '2017-02-23',
            '2017-02-24',
            '2017-02-22',
            '',
            123456,
            '',
            'This is a description',//desc
            'This title has content',//title
            '',//markdown
            '',//categories,
            '',//tags2
            '',//last_location_point.lat
            ''//last_location_point.lot
        ], $values);

        // Test unstructured Post
        $values = $formatter->formatRecordForCSV([
            'post_date' => '2017-02-22',
            'created' => '2017-02-23',
            'updated' => '2017-02-24',
            'id' => 1234,
            'title' => null,
            'form_id' => null,
            'content' => 'This is a description',
            'contact' => 123456,
            'status' => 'draft'
        ], $keyAttributes);
        // check that the format matches what is expected from the attribute list
        $this->assertEquals([
            1234,
            'draft',
            '2017-02-23',
            '2017-02-24',
            '2017-02-22',
            '',
            123456,
            'This is a description',//desc
            '',//desc
            '',//title
            '',//markdown
            '',//categories,
            '',//tags2
            '',//last_location_point.lat
            ''//last_location_point.lot
        ], $values);
    }
}
