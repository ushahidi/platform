<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the form_attribute model
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

class FormAttributeModelTest extends Unittest_TestCase {
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
					'key' => 'name',
					'label' => 'Full Name',
					'input' => 'text',
					'type' => 'varchar',
				)
			),
			array(
				// Valid form data
				array(
					'key' => 'sex',
					'label' => 'Sex',
					'input' => 'select',
					'type' => 'varchar',
					'options' => array(
						'm' => 'Male',
						'f' => 'Female'
						)
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
				// Invalid form data set 0 - No Data
				array()
			),
			array(
				// Invalid form data set 1 - Missing Key
				array(
					'label' => 'Sex',
					'input' => 'select',
					'type' => 'varchar',
					'options' => array(
						'm' => 'Male',
						'f' => 'Female'
						)
				)
			),
			array(
				// Invalid form data set 2 - Missing Label
				array(
					'key' => 'sex',
					'input' => 'select',
					'type' => 'varchar',
					'options' => array(
						'm' => 'Male',
						'f' => 'Female'
						)
				)
			),
			array(
				// Invalid form data set 3 - Invalid input type
				array(
					'key' => 'sex',
					'label' => 'Sex',
					'input' => 'unknown',
					'type' => 'varchar',
					'options' => array(
						'm' => 'Male',
						'f' => 'Female'
						)
				)
			),
			array(
				// Invalid form data set 4 - Invalid type
				array(
					'key' => 'sex',
					'label' => 'Sex',
					'input' => 'select',
					'type' => 'unknown',
					'options' => array(
						'm' => 'Male',
						'f' => 'Female'
						)
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
		$attribute = ORM::factory('Form_Attribute');
		$attribute->values($set);

		$is_valid = TRUE;
		$message = '';
		try
		{
			$attribute->check();
		}
		catch (Exception $e)
		{
			$message = json_encode($e->errors('models'));
			$is_valid = FALSE;
		}
		$this->assertTrue($is_valid, $message);
	}

	/**
	 * Test Validate Inalid Entries
	 *
	 * @dataProvider provider_validate_invalid
	 * @return void
	 */
	public function test_validate_invalid($set)
	{	
		$attribute = ORM::factory('Form_Attribute');
		$attribute->values($set);

		$is_valid = FALSE;
		$message = '';
		try
		{
			$attribute->check();
			$message = 'This entry qualifies as valid when it should be invalid';
		}
		catch (Exception $e)
		{
			$is_valid = TRUE;
		}
		$this->assertTrue($is_valid, $message);
	}
}