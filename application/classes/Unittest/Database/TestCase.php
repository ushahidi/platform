<?php defined('SYSPATH') or die('No direct script access.');

abstract class Unittest_Database_TestCase extends Kohana_Unittest_Database_TestCase {

	/**
	 * Creates a connection to the unittesting database
	 * Overriding to fix database type in DSN - must be lowercase
	 *
	 * @return PDO
	 */
	public function getConnection()
	{
		// Get the unittesting db connection
		$config = Kohana::$config->load('database.'.$this->_database_connection);

		if($config['type'] !== 'pdo')
		{
			$config['connection']['dsn'] = strtolower($config['type']).':'.
			'host='.$config['connection']['hostname'].';'.
			'dbname='.$config['connection']['database'];
		}

		$pdo = new PDO(
			$config['connection']['dsn'],
			$config['connection']['username'],
			$config['connection']['password']
		);

		return $this->createDefaultDBConnection($pdo, $config['connection']['database']);
	}

	/**
	 * Returns the database operation executed in test setup.
	 * Overriding to fix Mysql 5.5 truncate errors
	 *
	 * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
	 */
	protected function getSetUpOperation()
	{
		//return PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT();
		$cascadeTruncates = TRUE;
		return new PHPUnit_Extensions_Database_Operation_Composite(array(
			new Unittest_Database_Operation_MySQL55Truncate($cascadeTruncates),
			PHPUnit_Extensions_Database_Operation_Factory::INSERT()
		));
	}

}
