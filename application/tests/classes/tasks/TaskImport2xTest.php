<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Functional and Unit tests for the Ushahidi:Import2x minion task
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class TaskImport2xTest extends Unittest_Database_TestCase {
	public function setUp()
	{
		parent::setUp();

		// Hack: Make sessions use mock session class to prevent weird exceptions
		$this->getMockForAbstractClass('Session', array(), 'Session_Mock');
		$config = Kohana::$config->load('a1');
		$session = $config->get('session');
		$session['type'] = 'mock';
		$config->set('session', $session);
	}

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
	 * Test import from db
	 *
	 * @return void
	 */
	public function test_db_import()
	{
		// @todo fix this!
		$this->markTestSkipped("Cannot complete until all form endpoints on T846 are completed\n -- Single call form create disabled by D475");
		return;

		$config = Kohana::$config->load('database.TestImport2x');

		if (empty($config))
		{
			$this->markTestSkipped('Could not load "database.TestImport2x" config');
		}

		try	{
			$task = Minion_Task::factory(array(
				'task' => 'ushahidi:import2x',
				'source' => 'sql',
				'database' => $config['connection']['database'],
				'username' => $config['connection']['username'],
				'password' => $config['connection']['password'],
				'oauth-client-id' => 'demoapp',
				'oauth-client-secret' => 'demopass',
				'dest-username' => 'importadmin',
				'dest-password' => 'testing',
				'clean' => TRUE,
			));
			Log::$write_on_add = FALSE; // avoid dumping output to stdout
			$task->execute();
		}
		catch (Exception $e)
		{
			// Don't hide exception.
			//$this->fail("Minion task ushahidi:import2x threw an exception");
			throw $e;
		}

		// Assert output
		$this->expectOutputRegex("/.*
Created 'Classic Report Form', ID: 1.
Imported 20 tags.
Imported 152 posts.
Imported 12 users./");
	}

	/**
	 * Test import from api
	 *
	 * @return void
	 */
	public function test_api_import()
	{
		// todo: do we really care about keeping this? i say no.
		return $this->markTestSkipped('API import test disabled, it is too fragile');

		$config = Kohana::$config->load('database.TestImport2x');

		if (empty($config))
		{
			$this->markTestSkipped('Could not load "database.TestImport2x" config');
		}

		try	{
			$task = Minion_Task::factory(array(
				'task' => 'ushahidi:import2x',
				'source' => 'api',
				'url' => 'http://demo.ushahidi.com',
				'clean' => TRUE,
				'oauth-client-id' => 'demoapp',
				'oauth-client-secret' => 'demopass',
				'dest-username' => 'importadmin',
				'dest-password' => 'testing',
			));
			Log::$write_on_add = FALSE; // avoid dumping output to stdout
			$task->execute();
		}
		catch (Exception $e)
		{
			// Don't hide exception.
			//$this->fail("Minion task ushahidi:import2x threw an exception");
			throw $e;
		}

		// Assert output
		$this->expectOutputRegex("/.*
Created 'Classic Report Form', ID: 1.
Imported [0-9]+ tags.
Imported [0-9]+ posts.
Imported [0-9]+ users./");
	}

}
