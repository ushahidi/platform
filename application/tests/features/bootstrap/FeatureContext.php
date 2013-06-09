<?php

/**
 * Ushahidi Feature Context
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

// We'll use the unittest modules bootstrap to hook into Kohana
require_once __DIR__.'/../../../../modules/unittest/bootstrap.php';

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;
use Behat\Behat\Event\FeatureEvent;

require_once 'RestContext.php';
require_once 'PHPUnitFixtureContext.php';

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
		$this->useContext('mink', $minkContext = new MinkExtendedContext);
	}
	
	/** @BeforeFeature */
	public static function featureSetup(FeatureEvent $event)
	{
		$fixtureContext = new PHPUnitFixtureContext($event->getParameters());
		$fixtureContext->setUpDBTester('ushahidi/Base');
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
	}
	
	/** @BeforeScenario @resetFixture */
	public function scenarioTearDown()
	{
		$this->getSubcontext('PHPUnitFixtureContext')->tearDownDBTester('ushahidi/Base');
	}

}
