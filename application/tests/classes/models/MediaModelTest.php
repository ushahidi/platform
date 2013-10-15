<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Unit tests for the media model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class MediaModelTest extends Unittest_TestCase {

	/**
	 * Provider for test_validate_valid
	 *
	 * @access public
	 * @return array
	 */
	public function provider_validate_valid()
	{
		return array(
			array(
				// Valid form data
				array(
					'id' => 1,
					'caption' => 'at cocolin',
					'url' => 'http://domain.com/api/v2/media/1',
					'mime' => 'image/jpeg',
					'original_file_url' => 'http://domain.com/imagefly/w420-h420/media/uploads/j2o_1381815821_o.jpg',
					'original_width' => 400,
					'original_height'=> 500,
					'medium_file_url' => 'http://domain.com/imagefly/w800/media/uploads/j2o_1381815821_o.jpg',
					'medium_width' => 200,
					'medium_height' => 300,
					'thumbnail_file_url' => 'http://domain.com/imagefly/w70/media/uploads/j2o_1381815821_o.jpg',
					'thumbnail_width' => 100,
					'thumbnail_height' => 100,
					'created' => '2013-10-15T05:43:41+00:00',
					'updated' => '1970-01-01T00:00:00+00:00',
				)
			),
			array(
				// Valid form data
				array(
					'id' => 2,
					'caption' => 'at sendai',
					'url' => 'http://domain.com/api/v2/media/2',
					'mime' => 'image/jpeg',
					'original_file_url' => 'http://domain.com/media/upload/photos',
					'original_width' => 500,
					'original_height'=> 600,
					'medium_file_url' => 'http://domain.com/imagefly/w800/media/uploads/j2o_1381815821_o.jpg',
					'medium_width' => 200,
					'medium_height' => 300,
					'thumbnail_file_url' => 'http://domain.com/imagefly/w70/media/uploads/j2o_1381815821_o.jpg',
					'thumbnail_width' => 100,
					'thumbnail_height' => 100,
					'created' => '2013-10-15T05:43:41+00:00',
					'updated' => '1970-01-01T00:00:00+00:00',
				)
			),
			array(
				// Valid form data
				array(
					'id' => 3,
					'caption' => 'ihub',
					'url' => 'http://domain.com/api/v2/media/1',
					'mime' => 'image/jpeg',
					'original_file_url' => 'http://domain.com/media/upload/photos',
					'original_width' => 600,
					'original_height'=> 700,
					'medium_file_url' => 'http://domain.com/imagefly/w800/media/uploads/j2o_1381815821_o.jpg',
					'medium_width' => 600,
					'medium_height' => 500,
					'thumbnail_file_url' => 'http://domain.com/imagefly/w70/media/uploads/j2o_1381815821_o.jpg',
					'thumbnail_width' => 70,
					'thumbnail_height' => 70,
					'created' => '2013-10-15T05:43:41+00:00',
					'updated' => '1970-01-01T00:00:00+00:00',
				)
			),
		);
	}

	/**
	 * Provider for test_validate_invalid
	 *
	 * @access public
	 * @return array
	 */
	public function provider_validate_invalid()
	{
		return array(
			array(
				// Invalid media data 1 - No Data
				array()
			),
			array(
				// Valid media data
				array(
					'id' => 2,
					'caption' => 'at sendai',
					'url' => 'http://domain.com/api/v2/media/2',
					'mime' => 'image/jpeg',
					'original_file_url' => 'http://domain.com/media/upload/photos',
					'original_width' => 500,
					'original_height'=> 600,
					'medium_file_url' => 'http://domain.com/imagefly/w800/media/uploads/j2o_1381815821_o.jpg',
					'medium_width' => 200,
					'medium_height' => 300,
					'thumbnail_file_url' => 'http://domain.com/imagefly/w70/media/uploads/j2o_1381815821_o.jpg',
					'thumbnail_width' => 100,
					'thumbnail_height' => 100,
					'created' => '2013-10-15T05:43:41+00:00',
					'updated' => '1970-01-01T00:00:00+00:00',
				)
			),
			array(
				// Valid form data
				array(
					'id' => 3,
					'caption' => 'ihub',
					'url' => 'http://domain.com/api/v2/media/1',
					'mime' => 'image/jpeg',
					'original_file_url' => 'http://domain.com/media/upload/photos',
					'original_width' => 600,
					'original_height'=> 700,
					'medium_file_url' => 'http://domain.com/imagefly/w800/media/uploads/j2o_1381815821_o.jpg',
					'medium_width' => 600,
					'medium_height' => 500,
					'thumbnail_file_url' => 'http://domain.com/imagefly/w70/media/uploads/j2o_1381815821_o.jpg',
					'thumbnail_width' => 70,
					'thumbnail_height' => 70,
					'created' => '2013-10-15T05:43:41+00:00',
					'updated' => '1970-01-01T00:00:00+00:00',
				)
			),
		);
	}

	/**
	 * Test Validate Valid Entries
	 *
	 * @dataProvider provider_validate_valid
	 * @return void
	 */
	public function test_validate_valid($set)
	{
		$media = ORM::factory('Media');
		$media->values($set);

		try
		{
			$media->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			$this->fail('This entry qualifies as invalid when it should be valid: '.json_encode($e->errors('models')));
		}
	}

}