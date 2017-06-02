<?php

namespace Tests\Integration\Bootstrap;

use Behat\Behat\Context\Context;

class PHPUnitFixtureContext implements Context
{

	/**
	 * @var PHPUnit_Extensions_Database_ITester
	 */
	private $databaseTester;

	/**
	 * The kohana database connection that PHPUnit should use for this test
	 * @var string
	 */
	protected $database_connection = 'default';

	/** @BeforeFeature */
	public static function featureSetup(\Behat\Behat\Hook\Scope\BeforeFeatureScope $scope)
	{
		$fixtureContext = new static();
		$fixtureContext->setUpDBTester('ushahidi/Base');
		$fixtureContext->insertGeometryFixtures();
	}

	/** @AfterFeature */
	public static function featureTearDown(\Behat\Behat\Hook\Scope\AfterFeatureScope $scope)
	{
		$fixtureContext = new static();
		$fixtureContext->tearDownDBTester('ushahidi/Base');
	}

	/** @BeforeScenario @resetFixture */
	public function scenarioSetup()
	{
		$this->setUpDBTester('ushahidi/Base');
		$this->insertGeometryFixtures();
	}

	protected function insertGeometryFixtures()
	{
		$pdo_connection = $this->getConnection()->getConnection();
		$pdo_connection->query("INSERT INTO `post_point` (`id`, `post_id`, `form_attribute_id`, `value`)
			VALUES (1, 1, 8, POINT(12.123, 21.213));");
		$pdo_connection->query("INSERT INTO `post_point` (`id`, `post_id`, `form_attribute_id`, `value`)
			VALUES (7, 1, 8, POINT(12.223, 21.313));");
		$pdo_connection->query("INSERT INTO `post_point` (`id`, `post_id`, `form_attribute_id`, `value`)
			VALUES (2, 99, 8, POINT(11.123, 24.213));");
		$pdo_connection->query("INSERT INTO `post_point` (`id`, `post_id`, `form_attribute_id`, `value`)
			VALUES (3, 9999, 8, POINT(10.123, 26.213));");
		$pdo_connection->query("INSERT INTO `post_point` (`id`, `post_id`, `form_attribute_id`, `value`)
			VALUES (4, 95, 8, POINT(1, 1));");
		$pdo_connection->query("INSERT INTO `post_point` (`id`, `post_id`, `form_attribute_id`, `value`)
			VALUES (5, 95, 12, POINT(1.2, 0.5));");
		$pdo_connection->query("INSERT INTO `post_point` (`id`, `post_id`, `form_attribute_id`, `value`)
			VALUES (6, 97, 8, POINT(1, 1));");

		$pdo_connection->query("INSERT INTO `post_geometry` (`id`, `post_id`, `form_attribute_id`, `value`)
			VALUES (1, 1, 9,
				GeomFromText('MULTIPOLYGON (((40 40, 20 45, 45 30, 40 40)),
					((20 35, 45 20, 30 5, 10 10, 10 30, 20 35),
					(30 20, 20 25, 20 15, 30 20)))'));");
	}

	/**
	 * Creates a connection to the unittesting database
	 * Overriding to fix database type in DSN - must be lowercase
	 *
	 * @return PDO
	 */
	public function getConnection()
	{
		// Get the unittesting db connection
		$config = \Kohana::$config->load('database.'.$this->database_connection);

		if ($config['type'] !== 'pdo') {
			// Replace MySQLi with MySQL since MySQLi isn't valid for a DSN
			$type = $config['type'] === 'MySQLi' ? 'MySQL' : $config['type'];

			$config['connection']['dsn'] = strtolower($type).':'.
			'host='.$config['connection']['hostname'].';'.
			'dbname='.$config['connection']['database'];
		}

		$pdo = new \PDO(
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
		return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(
			__DIR__ . '/../../datasets/' . $dataset . '.yml'
		);
	}



	/** Call this in a BeforeScenario hook */
	public function setUpDBTester($dataset)
	{
		$this->databaseTester = null;

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
		$this->databaseTester = null;
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
		if (empty($this->databaseTester)) {
			$this->databaseTester = $this->newDatabaseTester();
		}
		return $this->databaseTester;
	}

	/**
	 * Creates a IDatabaseTester for this testCase.
	 *
	 * @return PHPUnit_Extensions_Database_ITester
	 */
	protected function newDatabaseTester()
	{
		return new \PHPUnit_Extensions_Database_DefaultTester($this->getConnection());
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
		$cascadeTruncates = true;
		return new \PHPUnit_Extensions_Database_Operation_Composite(array(
			new \Unittest_Database_Operation_MySQL55Truncate($cascadeTruncates),
			\PHPUnit_Extensions_Database_Operation_Factory::INSERT()
		));
	}

	/**
	 * Returns the database operation executed in test cleanup.
	 *
	 * @return PHPUnit_Extensions_Database_Operation_DatabaseOperation
	 */
	protected function getTearDownOperation()
	{
		return \PHPUnit_Extensions_Database_Operation_Factory::NONE();
	}

	/**
	 * Creates a new DefaultDatabaseConnection using the given PDO connection
	 * and database schema name.
	 *
	 * @param PDO $connection
	 * @param string $schema
	 * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
	 */
	protected function createDefaultDBConnection(\PDO $connection, $schema = '')
	{
		return new \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($connection, $schema);
	}
}
