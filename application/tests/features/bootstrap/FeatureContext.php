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
		$this->useContext('mink', $minkContext = new MinkExtendedContext);
	}

	/** @BeforeSuite */
	public static function setup($event)
	{
		// Unnecessary but leaving it anyway
		
		// Clean the DB before we start
		self::_clean_db();
	}

	/** @BeforeFeature */
	public static function featureSetup($event)
	{
		// this might need to run before every scenario, but
		// that would be a bit slow
		
		// Clean the DB before we start
		self::_clean_db();
		
		// Create Dummy form
		$form = ORM::factory("Form")
			->set('name', 'Dummy')
			->set('type', 'report')
			->set('description', 'Dummy')
			->set('id', 1)
			->save();
			
		// Create Dummy groups
		$group = ORM::factory("Form_Group")
			->set('label', 'Dummy')
			->set('priority', 99)
			->set('form_id', 1)
			->set('id', 1)
			->save();
			
		// Create Dummy attribute
		$attr = ORM::factory("Form_Attribute")
			->set('key', 'dummy_varchar')
			->set("label", "Dummy")
			->set("type", "varchar")
			->set("input", "text")
			->set("required", true)
			->set("priority", 1)
			->set('id', 1)
			->save();
		
		$group->add('form_attributes', $attr);
	}

	/**
	 * @BeforeFeature @post
	 */
	public static function setupFormForPost($event)
	{
		$form = ORM::factory('Form', 1);
		$group = ORM::factory('Form_Group', 1);
		
		// Create full_name attribute
		$attr = ORM::factory("Form_Attribute")
			->set('key', 'full_name')
			->set("label", "Full Name")
			->set("type", "varchar")
			->set("input", "text")
			->set("required", true)
			->set("priority", 1)
			->save();
		$group->add('form_attributes', $attr);
		
		// Create description attribute
		$attr = ORM::factory("Form_Attribute")
			->set('key', 'description')
			->set("label", "Description")
			->set("type", "text")
			->set("input", "textarea")
			->set("required", true)
			->set("priority", 2)
			->save();
		$group->add('form_attributes', $attr);
		
		// Create dob attribute
		$attr = ORM::factory("Form_Attribute")
			->set('key', 'date_of_birth')
			->set("label", "Date of birth")
			->set("type", "datetime")
			->set("input", "date")
			->set("required", false)
			->set("priority", 3)
			->save();
		$group->add('form_attributes', $attr);
		
		// Create missing_date attribute
		$attr = ORM::factory("Form_Attribute")
			->set('key', 'missing_date')
			->set("label", "Missing Date")
			->set("type", "datetime")
			->set("input", "date")
			->set("required", true)
			->set("priority", 4)
			->save();
		$group->add('form_attributes', $attr);
		
		// Create last_location attribute
		$attr = ORM::factory("Form_Attribute")
			->set('key', 'last_location')
			->set("label", "Last Location")
			->set("type", "varchar")
			->set("input", "text")
			->set("required", true)
			->set("priority", 5)
			->save();
		$group->add('form_attributes', $attr);
		
		// Create status attribute
		$attr = ORM::factory("Form_Attribute")
			->set('key', 'status')
			->set("label", "Status")
			->set("type", "varchar")
			->set("input", "select")
			->set("required", false)
			->set("options",
				array(
					"information_sought",
					"is_note_author",
					"believed_alive",
					"believed_missing",
					"believed_dead"
				))
			->set("priority", 6)
			->save();
		$group->add('form_attributes', $attr);
	}

	/**
	 * @BeforeScenario @searchPostFixture
	 */
	public function setupSearchPostFixture()
	{
		// Add posts with searchable data
		ORM::factory("Post")
			->set('form_id', 1)
			->set('title', 'Should be returned when Searching')
			->set('type', 'report')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 99)
			->set('locale', 'en_US')
			->save();
		ORM::factory("Post")
			->set('form_id', 1)
			->set('title', 'another report')
			->set('type', 'report')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 98)
			->set('locale', 'en_US')
			->save();
		ORM::factory("Post")
			->set('form_id', 1)
			->set('title', 'search by attribute')
			->set('type', 'report')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 97)
			->set('locale', 'en_US')
			->save();
		ORM::factory("Post_Varchar")
			->set('post_id', 97)
			->set('form_attribute_id', 1)
			->set('value', "special-string")
			->set('id', 50)
			->save();
		ORM::factory("Post")
			->set('form_id', 1)
			->set('title', 'French post to test Searching')
			->set('type', 'report')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 96)
			->set('locale', 'fr_FR')
			->save();
	}

	/**
	 * @AfterScenario @searchPostFixture
	 */
	public function teardownSearchPostFixture()
	{
		// Remove post
		ORM::factory("Post", 99)->delete();
		ORM::factory("Post", 98)->delete();
		ORM::factory("Post_Varchar", 50)->delete();
		ORM::factory("Post", 97)->delete();
		ORM::factory("Post", 96)->delete();
	}

	/**
	 * @BeforeFeature @revisionFixture
	 */
	public static function setupRevisionFixture()
	{
		// Add posts with searchable data
		ORM::factory("post")
			->set('form_id', 1)
			->set('title', 'Should be returned when Searching')
			->set('type', 'report')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 99)
			->set('locale', 'en_US')
			->save();
		ORM::factory("Post_Varchar")
			->set('post_id', 99)
			->set('form_attribute_id', 1)
			->set('value', "special-string")
			->set('id', 50)
			->save();
		
		ORM::factory("post")
			->set('form_id', 1)
			->set('title', 'Should be returned when Searching')
			->set('type', 'revision')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 101)
			->set('parent_id', 99)
			->set('locale', 'en_US')
			->save();
		ORM::factory("Post_Varchar")
			->set('post_id', 101)
			->set('form_attribute_id', 1)
			->set('value', "previous_string")
			->set('id', 51)
			->save();
		
		ORM::factory("post")
			->set('form_id', 1)
			->set('title', 'Should be returned when Searching')
			->set('type', 'revision')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 102)
			->set('parent_id', 99)
			->set('locale', 'en_US')
			->save();
		ORM::factory("Post_Varchar")
			->set('post_id', 102)
			->set('form_attribute_id', 1)
			->set('value', "special-string")
			->set('id', 52)
			->save();
	}

	/**
	 * @BeforeFeature @translationFixture
	 */
	public static function setupTranslationFixture()
	{
		// Add posts with searchable data
		ORM::factory("post")
			->set('form_id', 1)
			->set('title', 'Original post')
			->set('type', 'report')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 99)
			->set('locale', 'en_US')
			->save();
		ORM::factory("Post_Varchar")
			->set('post_id', 99)
			->set('form_attribute_id', 1)
			->set('value', "special-string")
			->set('id', 50)
			->save();
		
		ORM::factory("post")
			->set('form_id', 1)
			->set('title', 'French post')
			->set('type', 'translation')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 101)
			->set('parent_id', 99)
			->set('locale', 'fr_FR')
			->save();
		ORM::factory("Post_Varchar")
			->set('post_id', 101)
			->set('form_attribute_id', 1)
			->set('value', "french string")
			->set('id', 51)
			->save();
		
		ORM::factory("post")
			->set('form_id', 1)
			->set('title', 'French post')
			->set('type', 'revision')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 105)
			->set('parent_id', 101)
			->set('locale', 'fr_FR')
			->save();
		ORM::factory("Post_Varchar")
			->set('post_id', 105)
			->set('form_attribute_id', 1)
			->set('value', "french string")
			->set('id', 55)
			->save();
		
		ORM::factory("post")
			->set('form_id', 1)
			->set('title', 'Arabic post')
			->set('type', 'translation')
			->set('content', 'Some description')
			->set('status', 'published')
			->set('id', 102)
			->set('parent_id', 99)
			->set('locale', 'ar_AR')
			->save();
		ORM::factory("Post_Varchar")
			->set('post_id', 102)
			->set('form_attribute_id', 1)
			->set('value', "arabic string")
			->set('id', 52)
			->save();
	}
	
	/**
	 * @BeforeFeature @oauth2
	 */
	public static function setupOauth($event)
	{
		ORM::factory('OAuth_Client')
			->set('client_id', 'demoapp')
			->set('client_secret', 'demopass')
			->save();
		
		ORM::factory('OAuth_AuthorizationCode')
			->set('authorization_code', '4d105df9a7f8645ef8306dd40c7b1952794bf368')
			->set('client_id', 'demoapp')
			->set('scope', 'api')
			->set('expires', date('Y-m-d H:i:s', strtotime('+1 day')))
			->save();
		
		ORM::factory('OAuth_RefreshToken')
			->set('refresh_token', '5a846f5351a46fc9bdd5b8f55224b51671cf8b8f')
			->set('client_id', 'demoapp')
			->set('scope', 'api')
			->set('expires', date('Y-m-d H:i:s', strtotime('+10 day')))
			->save();
		
		ORM::factory('OAuth_AccessToken')
			->set('access_token', 'testingtoken')
			->set('client_id', 'demoapp')
			->set('scope', 'api posts forms')
			->set('expires', date('Y-m-d H:i:s', strtotime('+1 day')))
			->save();
		
		ORM::factory('User')
			->set('username', 'robbie')
			->set('password', 'testing')
			->set('email', 'robbie@ushahidi.com')
			->save();
		
		ORM::factory("Post")
			->set('form_id', 1)
			->set('title', 'Test post')
			->set('type', 'report')
			->set('locale', 'en_us')
			->set('content', 'testing post for oauth')
			->set('status', 'published')
			->set('id', 95)
			->save();
		
		ORM::factory('OAuth_Client')
			->set('client_id', 'restricted_app')
			->set('client_secret', 'demopass')
			->set('grant_types', array('authorization_code'))
			->save();
		
		ORM::factory('OAuth_AuthorizationCode')
			->set('authorization_code', '4d105df9a7f8645ef8306dd40c7b1952794bf372')
			->set('client_id', 'restricted_app')
			->set('scope', 'api')
			->set('expires', date('Y-m-d H:i:s', strtotime('+1 day')))
			->save();
	}
	
	/**
	 * Automatically set bearer token so you can forget about it
	 * @BeforeFeature @oauth2Skip
	 */
	public static function createDefaultBearerToken()
	{
		ORM::factory('OAuth_Client')
			->set('client_id', 'demoapp')
			->set('client_secret', 'demopass')
			->save();

		ORM::factory('OAuth_AccessToken', 'default')
			->set('access_token', 'defaulttoken')
			->set('client_id', 'demoapp')
			->set('scope', 'api posts forms')
			->set('expires', date('Y-m-d H:i:s', strtotime('+1 day')))
			->save();
	}
	
	/**
	 * Automatically set bearer token so you can forget about it
	 * @BeforeScenario @oauth2Skip
	 */
	public function setDefaultBearerAuth()
	{
		$this->getSubcontext('RestContext')->thatTheRequestHeaderIs('Authorization', 'Bearer defaulttoken');
	}

	/** @AfterSuite */
	public static function teardown($event)
	{
		self::_clean_db();
	}

	protected static function _clean_db()
	{
		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=0;")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE users")->execute();
		// Forms, Attributes, Groups
		DB::query(Database::DELETE, "TRUNCATE TABLE forms")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_groups")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_attributes")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_groups_form_attributes")->execute();
		// Posts & field values
		DB::query(Database::DELETE, "TRUNCATE TABLE posts")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_datetime")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_decimal")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_geometry")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_int")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_point")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_text")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_varchar")->execute();
		// Tags
		DB::query(Database::DELETE, "TRUNCATE TABLE tags")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE posts_tags")->execute();
		// Sets
		DB::query(Database::DELETE, "TRUNCATE TABLE sets")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE posts_sets")->execute();
		// oauth
		DB::query(Database::DELETE, "TRUNCATE TABLE oauth_clients")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE oauth_access_tokens")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE oauth_authorization_codes")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE oauth_refresh_tokens")->execute();
		
		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=1;")->execute();
	}
}