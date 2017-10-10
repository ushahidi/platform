<?php

/**
 * Unit tests for StatefulData Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Core\Traits;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class StatefulDataTest extends \PHPUnit\Framework\TestCase
{
    protected $test_post_data_current;
    protected $test_post_data_new;
    protected $changed_post_data;

    /**
        * Test setState method against a Post entity
        */
    public function testSetStatePost()
    {
        $this->setPostTestData();

        // Construct StaefulData for Current Post
        $mock = new MockPostData($this->test_post_data_current);

        // Set StaefulData from updated Post
        $entity = $mock->setState($this->test_post_data_new);

        $this->assertEquals('Updated Test Post', $entity->title);

        $this->assertEquals(true, $entity->hasChanged('values', 'full_name'));

        $this->assertEquals($this->changed_post_data, $entity->getChanged());
    }

    // POST DATA SECTION
    protected function setPostTestData()
    {
        $this->test_post_data_current = array (
            'id' => '110',
            'parent_id' => null,
            'form_id' => '1',
            'user_id' => '1',
            'type' => 'report',
            'title' => 'ACL test post',
            'slug' => null,
            'content' => 'Testing oauth posts api access',
            'author_email' => null,
            'author_realname' => null,
            'status' => 'published',
            'published_to' => '[]',
            'locale' => 'en_us',
            'created' => '1355743120',
            'updated' => null,
            'post_date' => '2012-12-17 03:18:40',
            'message_id' => '4',
            'source' => 'sms',
            'contact_id' => '3',
            'color' => null,
            'completed_stages' => [1]
        ) ;

        $this->test_post_data_new = array (
            'id' => '110',
            'parent_id' => null,
            'form_id' => '1',
            'user_id' => '4',
            'type' => 'report',
            'title' => 'Updated Test Post',
            'slug' => 'updated-test-post-596fe1a454e54',
            'content' => 'Testing oauth posts api access',
            'author_email' => null,
            'author_realname' => null,
            'status' => 'published',
            'published_to' => '[]',
            'locale' => 'en_us',
            'created' => '1355743120',
            'post_date' => '2012-12-17 03:18:40',
            'message_id' => '4',
            'source' => 'sms',
            'contact_id' => '3',
            'color' => null,
            'values' =>
            array (
                'missing_date' => array (
                    0 => '2012-09-25 00:00:00',
                ),
                'last_location_point' => array (
                0 => array (
                        'lon' => -85.39,
                        'lat' => 33.755,
                    ),
                ),
                'full_name' => array (
                    0 => 'David Kobia',
                ),
                'last_location' => array (
                        0 => 'atlanta',
                ),
                'missing_status' => array (
                    0 => 'believed_missing',
                ),
                'tags1' => array (
                    0 => '3',
                    1 => '4',
                ),
            ),
            'tags' => array (
                0 => '3',
                1 => '4',
            ),
            'sets' => array (
            ),
            'completed_stages' =>array (
            ),
        );

        $this->changed_post_data = array (
            'user_id' => 4,
            'title' => 'Updated Test Post',
            'slug' => 'updated-test-post-596fe1a454e54',
            'values' => array (
                'missing_date' => array (
                    0 => '2012-09-25 00:00:00',
                ),
                'last_location_point' => array (
                    0 => array (
                        'lon' => -85.390000000000001,
                        'lat' => 33.755000000000003,
                    ),
                ),
                'full_name' => array (
                    0 => 'David Kobia',
                ),
                'last_location' => array (
                    0 => 'atlanta',
                ),
                'missing_status' => array (
                    0 => 'believed_missing',
                ),
                'tags1' => array (
                    0 => '3',
                    1 => '4',
                ),
            ),
            'tags' => array (
                0 => '3',
                1 => '4',
            ),
            'sets' => array (
            ),
            'completed_stages' =>array (
            )
        );
    }

    protected function getPostDefinition()
    {
        return [
			'id'              => 'int',
			'parent_id'       => 'int',
			'form'            => false, /* alias */
			'form_id'         => 'int',
			'user'            => false, /* alias */
			'user_id'         => 'int',
			'type'            => 'string',
			'title'           => 'string',
			'slug'            => '*slug',
			'content'         => 'string',
			'author_email'    => 'string', /* @todo email filter */
			'author_realname' => 'string', /* @todo redundent with user record */
			'status'          => 'string',
			'created'         => 'int',
			'updated'         => 'int',
			'post_date'       => '*date',
			'locale'          => '*lowercasestring',
			'values'          => 'array',
			'tags'            => 'array',
			'published_to'    => '*json',
			'completed_stages'=> 'array',
			'sets'            => 'array',
		];
    }
}
