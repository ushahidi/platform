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

        echo "The changed_array for new null post values:".print_r($updated_entity->getChangedArray(), true );

        //TODO: WRONG! these changes should just be ignored!!!
        $this->assertArrayHasKey('values', $updated_entity->getChangedArray() );
        $this->assertEquals(1, count($updated_entity->getChangedArray()) );
    }

    public function testDetectedChangesForRemovedPostValues()
    {
        $original_entity = new MockPostData($this->getTestArrayWithValuesArray() );

        //create otherwise identical data, but change one element in values
        $new_data = $this->getTestArrayWithValuesArray();
        $new_data['values']['full_name'] = null;
        $new_data['values']['last_location'] = null;

        //setting the state with single changes.
        $updated_entity = $original_entity->setState($new_data); // setState with the same exact object

        echo "The changed_array for removed post values:".print_r($updated_entity->getChangedArray(), true );

        //assert that changed contains full_name, last_location and nothing else
        $this->assertArrayHasKey('full_name', $updated_entity->getChangedArray() );
        $this->assertArrayHasKey('last_location', $updated_entity->getChangedArray() );
        $this->assertEquals(2, count($updated_entity->getChangedArray()) );
    }
    public function testDetectedChangesForOneUpdatedPostValue()
    {
        $original_entity = new MockPostData($this->getTestArrayWithValuesArray() );

        //create otherwise identical data, but change one element in values
        $new_data = $this->getTestArrayWithValuesArray();
        $new_data['values']['full_name'] = 'Egbert Himmelgang';

        //setting the state with single changes.
        $updated_entity = $original_entity->setState($new_data); // setState with the same exact object

        //echo "The changed_array after updating one post value:".print_r($updated_entity->getChangedArray(), true );

        //assert that changed contains full_name and nothing else
        $this->assertArrayHasKey('full_name', $updated_entity->getChangedArray() );
        $this->assertEquals(1, count($updated_entity->getChangedArray()) );

    }

    public function testDetectedChangesForNewTagsValues()
    {
        //starting off with empty values array with empty tags1 array
        $original_data = $this->getTestArrayWithValuesArray();
        $original_data['values']['tags1'] = array();
        $original_data['values']['full_name'] = array();
        $original_entity = new MockPostData($original_data);

        //updating it with identical data + new tags array
        $new_data = $this->getTestArrayWithValuesArray();

        //now create the post object and set new state
        $updated_entity = $original_entity->setState($new_data); // setState with the same exact object

        echo "The changed_array for new post values:".print_r($original_entity->getChangedArray(), true );


        $this->assertTrue(array_key_exists('tags1', $updated_entity->getChangedArray()));
        $this->assertTrue(array_key_exists('full_name', $updated_entity->getChangedArray()));
    }


}
