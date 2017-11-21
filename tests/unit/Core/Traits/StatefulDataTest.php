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


    public function testCollectChangesToEntityUsingDates()
    {
        $original_entity = new MockPostData($this->getTestArrayWithValuesArray() );

        $new_data = $this->getTestArrayWithValuesArray();
        $new_data['post_date'] = '2017-11-20 11:08:40'; //change the date

        $original_entity = $original_entity->setState($new_data);
        $changed_array = $original_entity->getChangedArray();

        //since we only changed one item, there should only be one item in this array
        $this->assertEquals( 1, count($changed_array));
        $this->assertTrue( array_key_exists('post_date', $changed_array) );

    }

    public function testDetectedChangesWithIdenticalData()
    {
        $original_entity = new MockPostData($this->getTestArrayWithValuesArray() );
        $new_data = $this->getTestArrayWithValuesArray();

        //setting the state with NO CHANGES.
        $updated_entity = $original_entity->setState( $new_data ); // setState with the same exact object

        //assert that nothing has changed
        $this->assertEquals( 0, sizeof($updated_entity->getChanged()) );
    }

    public function testDetectedChangesWhenOneFirstLevelItemIsDifferent()
    {
        $original_entity = new MockPostData($this->getTestArrayWithValuesArray() );
        //create an array with identical data, but then change one element
        $new_data = $this->getTestArrayWithValuesArray();
        $new_data['color'] = 'some new color';

        //setting the state with single change.
        $original_entity->setState($new_data); // setState with the same exact object

        //assertions
        $this->assertEquals(1, sizeof($original_entity->getChanged()));
        $this->assertArrayHasKey("color", $original_entity->getChanged() );
        $this->assertEquals('some new color', $original_entity->getNewChangedValueForKey('color') );
    }

    public function testDetectedChangesWhenNewDataAddsBogusKey()
    {
        $original_entity = new MockPostData($this->getTestArrayWithValuesArray() );
        //create an array with identical data, but then change one element
        $new_data = $this->getTestArrayWithValuesArray();
        $new_data['bogus_key'] = 'some nonsense';

        //setting the state with bogus key added.
        $updated_entity = $original_entity->setState($new_data); // setState with the same exact object

        //assertions
        $message = "Intersected array SHOULD be empty because we don't know about this new key ";
        $this->assertEquals(0, count($updated_entity->getChanged()), $message);
    }

    public function testDetectedChangesAgainstNewNullPostValuesArray()
    {
        $original_entity = new MockPostData($this->getTestArrayWithValuesArray() );

        //create otherwise identical data, but null the values array
        $new_data = $this->getTestArrayWithValuesArray();
        $new_data['values'] = null;

        //set the state with this new data
        $updated_entity = $original_entity->setState($new_data); // setState with the same exact object

        //assert that the changes for 'values' are null
        $message = "The values array should not be changed";
        $this->assertEmpty( $updated_entity->getChanged()['values'], $message );
    }

    public function testDetectedChangesForOneUpdatedPostValue()
    {
        $original_entity = new MockPostData($this->getTestArrayWithValuesArray() );

        //create otherwise identical data, but change one element in values
        $new_data = $this->getTestArrayWithValuesArray();
        $new_data['values']['full_name'] = array('0'=>'Egbert Himmelgang');

        //setting the state with single changes.
        $updated_entity = $original_entity->setState($new_data); // setState with the same exact object

        //assert that values has changed
        $this->assertArrayHasKey('values', $updated_entity->getChanged() );

        //WHY ARE ALL VALUES MARKED AS CHANGED?
        $this->assertEquals(1, count($updated_entity->getChanged()['values'] ));
    }

    public function testDetectedChangesForAllNewPostValues()
    {
        //starting off with an empty values array
        $original_data = $this->getTestArrayWithValuesArray();
        $original_data['values'] = array('0'=>'0');
        $original_entity = new MockPostData($original_data);

        $new_data = $this->getTestArrayWithValuesArray();

        //now create the post object and set new state
        $updated_entity = $original_entity->setState($new_data); // setState with the same exact object

        //array should now have 6 elements
        $this->assertEquals(6, sizeof($updated_entity->getChanged()['values']));
    }

    /// TODO: test tags


    //  DATA SECTION

    protected function getTestArrayWithValuesArray()
    {
        return array(
                'id' => '110',
                'parent_id' => null,
                'form_id' => '1',
                'user_id' => '1',
                'type' => 'report',
                'title' => 'Test Data Original',
                'slug' => 'tests-data-original',
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
                'color' => 'no color!',
                'completed_stages' => [1],
                'values' => array (
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
                        0 => 'Bruce Kobia',
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
            );
        }



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
            'completed_stages' => [1],
            'values' => array (
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
                    0 => 'Bruce Kobia',
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
            'values' => array (
                'missing_date' => array (
                    0 => '2012-09-15 00:00:00',
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
                    0 => '2012-09-15 00:00:00',
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
