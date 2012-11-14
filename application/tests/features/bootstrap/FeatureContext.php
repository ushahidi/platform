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
		// Clean the DB before we start
		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=0;")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE forms")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_groups")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_attributes")->execute();
		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=1;")->execute();
	}

	/** @BeforeFeature */
	public static function featureSetup($event)
	{
		// Create Dummy form
		ORM::factory("form", 1)
			->set('name', 'Dummy')
			->set('type', 'report')
			->set('description', 'Dummy')
			->set('id', 1)
			->save();
			
		// Create Dummy groups
		ORM::factory("form_group", 1)
			->set('label', 'Dummy')
			->set('priority', 99)
			->set('form_id', 1)
			->set('id', 1)
			->save();
			
		// Create Dummy attribute
		ORM::factory("form_attribute", 1)
			->set('key', 'dummy')
			->set("label", "Dummy")
			->set("type", "varchar")
			->set("input", "text")
			->set("required", true)
			->set("priority", 1)
			->set('form_id', 1)
			->set('form_group_id', 1)
			->set('id', 1)
			->save();
	}

	/** @AfterSuite */
	public static function teardown($event)
	{
		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=0;")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE forms")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_groups")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_attributes")->execute();
		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=1;")->execute();
	}
}