<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the post model
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Unit Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class PostModelTest extends Unittest_TestCase {
	/**
	 * Create A Valid Form
	 * @return object $form - a valid form
	 */
	private function create_valid_form()
	{
		$form = ORM::factory('Form');

		try
		{
			$form->name = 'Test Form';
			$form->type = 'report';
			$form->description = 'Test Report Form';
			$form->save();
		}
		catch (Kohana_Exception $e)
		{
			$this->fail("Can't create form: ".Kohana_Debug::dump($e));
		}
		
		return $form;
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
		$form = $this->create_valid_form();

		return array(
			array(
				// Valid form data
				array(
					'form_id' => $form->id,
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
					'form_id' => $form->id,
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
		// Setup valid form
		$form = $this->create_valid_form();

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
					'form_id' => $form->id,
					'type' => 'unknown',
					'locale' => 'en_US',
					'title' => 'Test Post Title',
					'content' => 'Test Report Content',
				)
			)
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

		$is_valid = TRUE;
		$message = '';
		try
		{
			$post->check();
		}
		catch (Exception $e)
		{
			$message = json_encode($e->errors('models'));
			$is_valid = FALSE;
		}
		$this->assertTrue($is_valid, $message);
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

		$is_valid = FALSE;
		$message = '';
		try
		{
			$post->check();
			$message = 'This entry qualifies as valid when it should be invalid';
		}
		catch (Exception $e)
		{
			$is_valid = TRUE;
		}
		$this->assertTrue($is_valid, $message);
	}
}