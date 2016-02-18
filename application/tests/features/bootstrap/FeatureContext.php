<?php

/**
 * Ushahidi Feature Context
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// Load bootstrap to hook into Kohana
require_once __DIR__.'/../../bootstrap.php';

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;
use Behat\Behat\Event\FeatureEvent;

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param   array   $parameters     context parameters (set them up through behat.yml)
	 */
	public function __construct(array $parameters)
	{
		// Initialize your context here
		$this->useContext('RestContext', new RestContext($parameters));
		$this->useContext('PHPUnitFixtureContext', new PHPUnitFixtureContext($parameters));
		$this->useContext('MinkContext', $minkContext = new MinkExtendedContext);
	}

	/** @BeforeFeature */
	public static function featureSetup(FeatureEvent $event)
	{
		$fixtureContext = new PHPUnitFixtureContext($event->getParameters());
		$fixtureContext->setUpDBTester('ushahidi/Base');

		$pdo_connection = $fixtureContext->getConnection()->getConnection();
		self::insertGeometryFixtures($pdo_connection);
	}

	/** @AfterFeature */
	public static function featureTearDown(FeatureEvent $event)
	{
		$fixtureContext = new PHPUnitFixtureContext($event->getParameters());
		$fixtureContext->tearDownDBTester('ushahidi/Base');
	}

	/** @BeforeScenario @resetFixture */
	public function scenarioSetup()
	{
		$this->getSubcontext('PHPUnitFixtureContext')->setUpDBTester('ushahidi/Base');

		$pdo_connection = $this->getSubcontext('PHPUnitFixtureContext')->getConnection()->getConnection();
		self::insertGeometryFixtures($pdo_connection);
	}

	/** @BeforeScenario @private */
	public function makePrivate()
	{
		$config = Kohana::$config->load('site');
		$config->set('private', true);

		$config = Kohana::$config->load('features');
		$config->set('private.enabled', true);
	}

	/** @AfterScenario @private */
	public function makePublic()
	{
		$config = Kohana::$config->load('site');
		$config->set('private', false);

		$config = Kohana::$config->load('features');
		$config->set('private.enabled', false);
	}

	/** @BeforeScenario @rolesEnabled */
	public function enableRoles()
	{
		$config = Kohana::$config->load('features');
		$config->set('roles.enabled', true);
	}

	/** @AfterScenario @rolesEnabled */
	public function disableRoles()
	{
		$config = Kohana::$config->load('features');
		$config->set('roles.enabled', false);
	}

	/** @BeforeScenario @dataImportEnabled */
	public function enableDataImport()
	{
		$config = Kohana::$config->load('features');
		$config->set('data-import.enabled', true);
	}

	/** @AfterScenario @dataImportEnabled */
	public function disableDataImport()
	{
		$config = Kohana::$config->load('features');
		$config->set('data-import.enabled', false);
	}

	protected static function insertGeometryFixtures($pdo_connection)
	{
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
	 * Automatically set bearer token so you can forget about it
	 * @BeforeScenario @oauth2Skip
	 */
	public function setDefaultBearerAuth()
	{
		$this->getSubcontext('RestContext')->thatTheRequestHeaderIs('Authorization', 'Bearer defaulttoken');
	}

}
