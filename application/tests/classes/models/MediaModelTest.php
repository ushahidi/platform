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
					'caption' => 'at the ihub',
					'mime' => 'application\/octet-stream',
					'file_url' => 'http://domain.com/media/upload/photos',
					'o_width' => 600,
					'o_height'=> 700,
					'm_filename' => '51f75f03cb6fe1.02579520_o.jpg',
					'm_width' => 300,
					'm_height' => 400,
					't_filename' => '51f75f03cb6fe1.02579520_t.jpg',
					't_width' => 100,
					't_height' => 100,
				)
			),
			array(
				// Valid form data
				array(
					'id' => 2,
					'caption' => 'at the sendai',
					'mime' => 'application\/octet-stream',
					'file_url' => 'http://domain.com/media/upload/photos',
					'o_width' => 500,
					'o_height'=> 600,
					'm_filename' => '51f75f03cb6fe1.02579520_o.jpg',
					'm_width' => 200,
					'm_height' => 300,
					't_filename' => '51f75f03cb6fe1.02579520_t.jpg',
					't_width' => 100,
					't_height' => 100,
				)
			),
			array(
				// Valid form data
				array(
					'id' => 3,
					'caption' => 'at the cocolin',
					'mime' => 'application\/octet-stream',
					'file_url' => 'http://domain.com/media/upload/photos',
					'o_width' => 400,
					'o_height'=> 500,
					'm_filename' => '51f75f03cb6fe1.02579520_o.jpg',
					'm_width' => 200,
					'm_height' => 300,
					't_filename' => '51f75f03cb6fe1.02579520_t.jpg',
					't_width' => 100,
					't_height' => 100,
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
					'caption' => 'at',
					'mime' => 'application\/octet-stream',
					'file_url' => 'http://domain.com/media/upload/photos',
					'o_width' => 500,
					'o_height'=> 600,
					'm_filename' => '51f75f03cb6fe1.02579520_o.jpg',
					'm_width' => 200,
					'm_height' => 300,
					't_filename' => '51f75f03cb6fe1.02579520_t.jpg',
					't_width' => 100,
					't_height' => 100,
				)
			),
			array(
				// Valid form data
				array(
					'id' => 3,
					'caption' => 'a',
					'mime' => 'application\/octet-stream',
					'file_url' => 'http://domain.com/media/upload/photos',
					'o_width' => 400,
					'o_height'=> 500,
					'm_filename' => '51f75f03cb6fe1.02579520_o.jpg',
					'm_width' => 200,
					'm_height' => 300,
					't_filename' => '51f75f03cb6fe1.02579520_t.jpg',
					't_width' => 100,
					't_height' => 100,
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
			$this->fail('This entry qualifies as invalid when it should be valid: '. json_encode($e->errors('models')));
		}
	}

}