<?php
// We'll use the unittest modules bootstrap to hook into Kohana
require_once __DIR__.'/../../../../modules/unittest/bootstrap.php';

use Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode;

require_once 'RestContext.php';

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
	}

	/** @BeforeSuite */
	public static function setup($event)
	{
		
	}

	/** @AfterSuite */
	public static function teardown($event)
	{
		DB::query(Database::DELETE, "TRUNCATE TABLE forms")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_groups")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_attributes")->execute();
	}
}