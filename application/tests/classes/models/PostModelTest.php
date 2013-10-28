<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the post model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class PostModelTest extends Unittest_Database_TestCase {

	/**
	 * Get data set PostPointModel
	 *
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet()
	{
		return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
			Kohana::find_file('tests/datasets', 'ushahidi/Base', 'yml')
		);
	}

	/**
	 * Provider for test_validate_valid
	 *
	 * @access public
	 * @return array
	 */
	public function provider_validate_valid()
	{
		// Setup valid form
		return array(
			array(
				// Valid form data
				array(
					'form_id' => 1,
					'title' => 'This is a valid Post',
					'locale' => 'en_US',
					'type' => 'report',
					'status' => 'published',
					'content' => 'Test Report Content',
				)
			),
			array(
				// Valid form data
				array(
					'form_id' => 1,
					'title' => 'This is a valid Post',
					'locale' => 'en_US',
					'type' => 'comment'
				)
			)
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
				// Invalid post data set 1 - No Data
				array()
			),
			array(
				// Invalid post data set 2 - Missing Form ID
				array(
					'type' => 'report',
					'title' => 'Test Post Title',
					'locale' => 'en_US',
					'content' => 'Test Report Content',
				)
			),
			array(
				// Invalid post data set 3 - Invalid Form ID
				array(
					'form_id' => 999999999,
					'type' => 'report',
					'locale' => 'en_US',
					'title' => 'Test Post Title',
					'content' => 'Test Report Content',
				)
			),
			array(
				// Invalid post data set 3 - Invalid Type
				array(
					'form_id' => 1,
					'type' => 'unknown',
					'locale' => 'en_US',
					'title' => 'Test Post Title',
					'content' => 'Test Report Content',
				)
			),
			array(
				// Invalid post data set 4 - Junk ID
				array(
					'id' => 'abc123',
					'form_id' => 1,
					'type' => 'report',
					'title' => 'Test Post Title',
					'locale' => 'en_US',
					'content' => 'Test Report Content',
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
		$post = ORM::factory('Post');
		$post->values($set);

		try
		{
			$post->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			$this->fail('This entry qualifies as invalid when it should be valid: '. json_encode($e->errors('models')));
		}
	}

	/**
	 * Test Validate Invalid Entries
	 *
	 * @dataProvider provider_validate_invalid
	 * @return void
	 */
	public function test_validate_invalid($set)
	{
		$post = ORM::factory('Post');
		$post->values($set);
		// Set ID too so we can test validation here
		$post->values($set, array('id'));

		try
		{
			$post->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}