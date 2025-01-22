<?php /** @noinspection ALL */

namespace Ushahidi\Tests\Integration\Bootstrap;

use Behat\Behat\Context\Context;
use Ushahidi\Tests\Integration\Bootstrap\Database\DefaultTester;
use Ushahidi\Tests\Integration\Bootstrap\Database\DefaultConnection;
use Ushahidi\Tests\Integration\Bootstrap\Database\Operation\Factory;
use Ushahidi\Tests\Integration\Bootstrap\Database\Dataset\YamlDataset;
use Ushahidi\Tests\Integration\Bootstrap\Database\Operation\Composite;

class PHPUnitFixtureContext implements Context
{
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
        $pdo_connection->query("INSERT INTO `post_point` (`id`, `post_id`, `form_attribute_id`, `value`)
            VALUES (8, 1690, 36, POINT(10.1235, 26.2135));");

        $pdo_connection->query("INSERT INTO `post_geometry` (`id`, `post_id`, `form_attribute_id`, `value`)
            VALUES (1, 1, 9,
                ST_GeomFromText('MULTIPOLYGON (((40 40, 20 45, 45 30, 40 40)),
                    ((20 35, 45 20, 30 5, 10 10, 10 30, 20 35),
                    (30 20, 20 25, 20 15, 30 20)))'));");
    }

    /** @BeforeScenario @private */
    public function makePrivate()
    {
        $this->setConfig('site', 'private', 'true');
        $this->setConfig('feature', 'private', '{"enabled":true}');
    }

    /** @AfterScenario @private */
    public function makePublic()
    {
        $this->setConfig('site', 'private', 'false');
        $this->setConfig('feature', 'private', '{"enabled":false}');
    }

    /** @BeforeScenario @disableRegistration */
    public function enableDisableRegistration()
    {
        $this->setConfig('site', 'disable_registration', 'true');
        $this->setConfig('feature', 'disable_registration', '{"enabled":true}');
    }

    /** @AfterScenario @disableRegistration */
    public function disableDisableRegistration()
    {
        $this->setConfig('site', 'disable_registration', 'false');
        $this->setConfig('feature', 'disable_registration', '{"enabled":false}');
    }

    /**
     * @BeforeScenario @rolesEnabled
     **/
    public function enableRoles()
    {
        $this->setConfig('feature', 'roles', '{"enabled":true}');
    }

    /**
     * @BeforeScenario @rolesDisabled
     **/
    public function disableRoles()
    {
        $this->setConfig('feature', 'private', '{"enabled":false}');
    }

    /**
     * @BeforeScenario @hxlEnabled
     **/
    public function enableHxl()
    {
        $this->setConfig('feature', 'hxl', '{"enabled":true}');
    }

    /**
     * @BeforeScenario @hxlDisabled
     **/
    public function disableHxl()
    {
        $this->setConfig('feature', 'hxl', '{"enabled":false}');
    }

    /** @BeforeScenario @webhooksEnabled */
    public function enableWebhooks()
    {
        $this->setConfig('feature', 'webhooks', '{"enabled":true}');
    }

    /** @AfterScenario @webhooksEnabled */
    public function disableWebhooks()
    {
        $this->setConfig('feature', 'webhooks', '{"enabled":false}');
    }

    /** @BeforeScenario @dataImportEnabled */
    public function enableDataImport()
    {
        $this->setConfig('feature', 'data-import', '{"enabled":true}');
    }

    /** @AfterScenario @dataImportEnabled */
    public function disableDataImport()
    {
        $this->setConfig('feature', 'data-import', '{"enabled":false}');
    }

    protected function setConfig($group, $key, $value)
    {
        $pdo_connection = $this->getConnection()->getConnection();
        $pdo_connection->query("
          INSERT INTO `config`
          (`group_name`, `config_key`, `config_value`) VALUES ('$group', '$key', '$value')
          ON DUPLICATE KEY UPDATE `config_value` = '$value';
        ");
    }

    /**
     * Creates a connection to the unittesting database
     * Overriding to fix database type in DSN - must be lowercase
     *
     */
    public function getConnection()
    {
        // Get the unittesting db connection
        $config = config('ohanzee-db.'.$this->database_connection);

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

    /** Call this in a BeforeScenario hook */
    public function setUpDBTester($dataset)
    {
        $this->databaseTester = null;

        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataset($this->getDataset($dataset));
        $this->getDatabaseTester()->onSetUp();
    }

    /** Call this in an AfterScenario hook */
    public function tearDownDBTester($dataset)
    {
        $this->getDatabaseTester()->setTearDownOperation($this->getTearDownOperation());
        $this->getDatabaseTester()->setDataset($this->getDataset($dataset));
        $this->getDatabaseTester()->onTearDown();

        /**
         * Destroy the tester after the test is run to keep DB connections
         * from piling up.
         */
        $this->databaseTester = null;
    }

    /**
     * Returns the test dataset.
     *
     * @param string|array $dataset Dataset filename
     */
    protected function getDataset($dataset)
    {
        return new YamlDataset(
            __DIR__ . '/../../datasets/' . $dataset . '.yml'
        );
    }

    /**
     * Gets the database tester for this testCase. If the database tester is
     * not set yet, this method calls newDatabaseTester() to obtain a new
     * instance.
     *
     */
    protected function getDatabaseTester()
    {
        if (empty($this->databaseTester)) {
            $this->databaseTester = $this->newDatabaseTester();
        }
        return $this->databaseTester;
    }

    /**
     * Creates a Database Tester for this testCase.
     *
     *      */
    protected function newDatabaseTester()
    {
        return new DefaultTester($this->getConnection());
    }

    /**
     * Returns the database operation executed in test setup.
     * Overriding to fix Mysql 5.5 truncate errors
     *
     */
    protected function getSetUpOperation()
    {
        $cascadeTruncates = true;
        return new Composite([
            Factory::TRUNCATE($cascadeTruncates),
            Factory::INSERT()
        ]);
    }

    /**
     * Returns the database operation executed in test cleanup.
     *
     */
    protected function getTearDownOperation()
    {
        return Factory::NONE();
    }

    /**
     * Creates a new DefaultDatabaseConnection using the given PDO connection
     * and database schema name.
     *
     * @param \PDO $connection
     * @param string $schema
     */
    protected function createDefaultDBConnection(\PDO $connection, $schema = '')
    {
        return new DefaultConnection($connection, $schema);
    }
}
