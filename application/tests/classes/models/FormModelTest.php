<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the form model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class FormModelTest extends Unittest_TestCase {
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
					'name' => 'Test Name',
					'type' => 'report',
					'description' => 'Test Report Description',
				)
			),
			array(
				// Valid form data
				array(
					'name' => 'Test Comment',
					'type' => 'comment',
					'description' => 'Test Comment Description',
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
				// Invalid form data set 1 - No Data
				array()
			),
			array(
				// Invalid form data set 2 - Missing Name
				array(
					'type' => 'report',
					'description' => 'Test Report Description',
				)
			),
			array(
				// Invalid form data set 3 - Missing Form Type
				array(
					'name' => 'Test Name',
					'description' => 'Test Report Description',
				)
			),
			array(
				// Invalid form data set 4 - Invalid type
				array(
					'name' => 'Test Name',
					'type' => 'unknown',
					'description' => 'Test Report Description',
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
		$form = ORM::factory('Form');
		$form->values($set);

		try
		{
			$form->check();
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
		$form = ORM::factory('Form');
		$form->values($set);

		try
		{
			$form->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}