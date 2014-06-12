<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the form_attribute model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
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

		try
		{
			$attribute->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			$this->fail('This entry qualifies as invalid when it should be valid: '. json_encode($e->errors('models')));
		}
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

		try
		{
			$attribute->check();
		}
		catch (ORM_Validation_Exception $e)
		{
			return;
		}

		$this->fail('This entry qualifies as valid when it should be invalid');
	}
}