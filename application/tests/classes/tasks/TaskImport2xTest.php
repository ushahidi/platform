<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * Functional and Unit tests for the Ushahidi:Import2x minion task
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class TaskImport2xTest extends Unittest_TestCase {
	public function setUp()
	{
		parent::setUp();
		
		// Hack: Make sessions use mock session class to prevent weird exceptions
		Session::$default = 'mock';
		Kohana::$config->load('auth')->set('session_type', 'mock');
		$this->getMockForAbstractClass('Session', array(), 'Session_Mock');
	}
	
	/**
	 * Test import from db
	 *
	 * @return void
	 */
	public function test_db_import()
	{
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
				'dest-username' => 'robbie',
				'dest-password' => 'testing',
				'clean' => TRUE,
			));
			Log::$write_on_add = FALSE; // avoid dumping output to stdout
			$task->execute();
		}
		catch (Exception $e)
		{
			$this->fail("Minion task ushahidi:import2x threw an exception");
		}
		
		// Assert output
		$this->expectOutputRegex("/.*
Created 'Classic Report Form', ID: 1.
Imported 20 tags.
Imported 152 posts.
Imported 11 users./");
	}

	/**
	 * Test import from api
	 *
	 * @return void
	 */
	public function test_api_import()
	{
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
				'dest-username' => 'robbie',
				'dest-password' => 'testing',
			));
			Log::$write_on_add = FALSE; // avoid dumping output to stdout
			$task->execute();
		}
		catch (Exception $e)
		{
			$this->fail("Minion task ushahidi:import2x threw an exception");
		}
		
		// Assert output
		$this->expectOutputRegex("/.*
Created 'Classic Report Form', ID: 1.
Imported [0-9]+ tags.
Imported [0-9]+ posts.
Imported [0-9]+ users./");
	}

}