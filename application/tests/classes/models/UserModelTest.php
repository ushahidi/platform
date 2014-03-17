<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Unit tests for the user model
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class UserModelTest extends Unittest_Database_TestCase {

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
	 * Test that an empty email is saved as NULL
	 *
	 * @return void
	 */
	public function test_empty_email_saved_as_null()
	{
		$user = ORM::factory('User')
			->set('username', 'Test1')
			->set('email', '')
			->save();

		// Using assertTrue because we need === not ==
		$this->assertTrue($user->email === NULL, 'Empty email value was not stored as NULL');
	}

	/**
	 * Test saving multiple users with empty emails
	 *
	 * This failed previously as both emails were saved as "" not NULL
	 * and unique key constaint fails
	 *
	 * @return void
	 */
	public function test_users_with_empty_emails()
	{
		try
		{
			$user2 = ORM::factory('User')
				->set('username', 'Test2')
				->set('email', '')
				->save();

			$user3 = ORM::factory('User')
				->set('username', 'Test3')
				->set('email', '')
				->save();
		}
		catch (Database_Exception $e)
		{
			$this->fail('Could not save multiple users with empty emails. Database exception: '. json_encode($e->errors('models')));
		}

	}
}