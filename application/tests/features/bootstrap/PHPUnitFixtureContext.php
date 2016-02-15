<?php
use Behat\Behat\Context\BehatContext;

class PHPUnitFixtureContext extends BehatContext {

	/**
	 * @var PHPUnit_Extensions_Database_ITester
	 */
	private $_databaseTester;

	/**
	 * The kohana database connection that PHPUnit should use for this test
	 * @var string
	 */
	protected $_database_connection = 'default';

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
			// Replace MySQLi with MySQL since MySQLi isn't valid for a DSN
			$type = $config['type'] === 'MySQLi' ? 'MySQL' : $config['type'];

			$config['connection']['dsn'] = strtolower($type).':'.
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
	 * Returns the test dataset.
	 *
	 * @param string|array $dataset Dataset filename
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	protected function getDataSet($dataset)
	{
		$file = Kohana::find_file('tests/datasets', $dataset , 'yml');

		return new PHPUnit_Extensions_Database_DataSet_YamlDataSet(
			$file
		);
	}



	/** Call this in a BeforeScenario hook */
	public function setUpDBTester($dataset)
	{
		$this->_databaseTester = NULL;

		$this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
		$this->getDatabaseTester()->setDataSet($this->getDataSet($dataset));
		$this->getDatabaseTester()->onSetUp();
	}

	/** Call this in an AfterScenario hook */
	public function tearDownDBTester($dataset)
	{
		$this->getDatabaseTester()->setTearDownOperation($this->getTearDownOperation());
		$this->getDatabaseTester()->setDataSet($this->getDataSet($dataset));
		$this->getDatabaseTester()->onTearDown();

		/**
		 * Destroy the tester after the test is run to keep DB connections
		 * from piling up.
		 */
		$this->_databaseTester = NULL;
	}

	/**
	 * Gets the IDatabaseTester for this testCase. If the IDatabaseTester is
	 * not set yet, this method calls newDatabaseTester() to obtain a new
	 * instance.
	 *
	 * @return PHPUnit_Extensions_Database_ITester
	 */
	protected function getDatabaseTester()
	{
		if (empty($this->_databaseTester))
		{
			$this->_databaseTester = $this->newDatabaseTester();
		}
		return $this->_databaseTester;
	}

	/**
	 * Creates a IDatabaseTester for this testCase.
	 *
	 * @return PHPUnit_Extensions_Database_ITester
	 */
	protected function newDatabaseTester()
	{
		return new PHPUnit_Extensions_Database_DefaultTester($this->getConnection());
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

	/**
	 * Returns the database operation executed in test cleanup.
	 *
	 * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
	 */
	protected function getTearDownOperation()
	{
		return PHPUnit_Extensions_Database_Operation_Factory::NONE();
	}

	/**
	 * Creates a new DefaultDatabaseConnection using the given PDO connection
	 * and database schema name.
	 *
	 * @param PDO $connection
	 * @param string $schema
	 * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 */
	protected function createDefaultDBConnection(PDO $connection, $schema = '')
	{
		return new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($connection, $schema);
	}

}
