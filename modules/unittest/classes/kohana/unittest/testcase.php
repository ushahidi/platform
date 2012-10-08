<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A version of the stock PHPUnit testcase that includes some extra helpers
 * and default settings
 */
abstract class Kohana_Unittest_TestCase extends PHPUnit_Framework_TestCase {
	
	/**
	 * Whether we should enable work arounds to make the tests compatible with phpunit 3.4
	 * @var boolean
	 */
	protected static $_assert_type_compatability = NULL;

	/**
	 * Make sure PHPUnit backs up globals
	 * @var boolean
	 */
	protected $backupGlobals = FALSE;

	/**
	 * A set of unittest helpers that are shared between normal / database
	 * testcases
	 * @var Kohana_Unittest_Helpers
	 */
	protected $_helpers = NULL;

	/**
	 * A default set of environment to be applied before each test
	 * @var array
	 */
	protected $environmentDefault = array();

	/**
	 * Creates a predefined environment using the default environment
	 *
	 * Extending classes that have their own setUp() should call
	 * parent::setUp()
	 */
	public function setUp()
	{
		if(self::$_assert_type_compatability === NULL)
		{
			if( ! class_exists('PHPUnit_Runner_Version'))
			{
				require_once 'PHPUnit/Runner/Version.php';
			}

			self::$_assert_type_compatability = version_compare(PHPUnit_Runner_Version::id(), '3.5.0', '<=');
		}

		$this->_helpers = new Unittest_Helpers;

		$this->setEnvironment($this->environmentDefault);
	}

	/**
	 * Restores the original environment overriden with setEnvironment()
	 *
	 * Extending classes that have their own tearDown()
	 * should call parent::tearDown()
	 */
	public function tearDown()
	{
		$this->_helpers->restore_environment();
	}

	/**
	 * Removes all kohana related cache files in the cache directory
	 */
	public function cleanCacheDir()
	{
		return Unittest_Helpers::clean_cache_dir();
	}

	/**
	 * Helper function that replaces all occurences of '/' with
	 * the OS-specific directory separator
	 *
	 * @param string $path The path to act on
	 * @return string
	 */
	public function dirSeparator($path)
	{
		return Unittest_Helpers::dir_separator($path);
	}

	/**
	 * Allows easy setting & backing up of enviroment config
	 *
	 * Option types are checked in the following order:
	 *
	 * * Server Var
	 * * Static Variable
	 * * Config option
	 *
	 * @param array $environment List of environment to set
	 */
	public function setEnvironment(array $environment)
	{
		return $this->_helpers->set_environment($environment);
	}

	/**
	 * Check for internet connectivity
	 *
	 * @return boolean Whether an internet connection is available
	 */
	public function hasInternet()
	{
		return Unittest_Helpers::has_internet();
	}

	/**
	 * Asserts that a variable is of a given type.
	 *
	 * @param string $expected
	 * @param mixed  $actual
	 * @param string $message
	 * @since Method available since Release 3.5.0
	 */
	public static function assertInstanceOf($expected, $actual, $message = '')
	{
		if(self::$_assert_type_compatability)
		{
			return self::assertType($expected, $actual, $message);
		}

		return parent::assertInstanceOf($expected, $actual, $message);
	}
	
	/**
	 * Asserts that an attribute is of a given type.
	 *
	 * @param string $expected
	 * @param string $attributeName
	 * @param mixed  $classOrObject
	 * @param string $message
	 * @since Method available since Release 3.5.0
	 */
	public static function assertAttributeInstanceOf($expected, $attributeName, $classOrObject, $message = '')
	{
		if(self::$_assert_type_compatability)
		{
			return self::assertAttributeType($expected, $attributeName, $classOrObject, $message);
		}

		return parent::assertAttributeInstanceOf($expected, $attributeName, $classOrObject, $message);
	}

	/**
	 * Asserts that a variable is not of a given type.
	 *
	 * @param string $expected
	 * @param mixed  $actual
	 * @param string $message
	 * @since Method available since Release 3.5.0
	 */
	public static function assertNotInstanceOf($expected, $actual, $message = '')
	{
		if(self::$_assert_type_compatability)
		{
			return self::assertNotType($expected, $actual, $message);
		}

		return self::assertNotInstanceOf($expected, $actual, $message);
	}

	/**
	 * Asserts that an attribute is of a given type.
	 *
	 * @param string $expected
	 * @param string $attributeName
	 * @param mixed  $classOrObject
	 * @param string $message
	 * @since Method available since Release 3.5.0
	 */
	public static function assertAttributeNotInstanceOf($expected, $attributeName, $classOrObject, $message = '')
	{
		if(self::$_assert_type_compatability)
		{
			return self::assertAttributeNotType($expected, $attributeName, $classOrObject, $message);
		}

		return self::assertAttributeNotInstanceOf($expected, $attributeName, $classOrObject, $message);
	}

	/**
	 * Asserts that a variable is of a given type.
	 *
	 * @param string $expected
	 * @param mixed  $actual
	 * @param string $message
	 * @since Method available since Release 3.5.0
	 */
	public static function assertInternalType($expected, $actual, $message = '')
	{
		if(self::$_assert_type_compatability)
		{
			return self::assertType($expected, $actual, $message);
		}
		
		return parent::assertInternalType($expected, $actual, $message);
	}

	/**
	 * Asserts that an attribute is of a given type.
	 *
	 * @param string $expected
	 * @param string $attributeName
	 * @param mixed  $classOrObject
	 * @param string $message
	 * @since Method available since Release 3.5.0
	 */
	public static function assertAttributeInternalType($expected, $attributeName, $classOrObject, $message = '')
	{
		if(self::$_assert_type_compatability)
		{
			return self::assertAttributeType($expected, $attributeName, $classOrObject, $message);
		}

		return self::assertAttributeInternalType($expected, $attributeName, $classOrObject, $message);
	}

	/**
	 * Asserts that a variable is not of a given type.
	 *
	 * @param string $expected
	 * @param mixed  $actual
	 * @param string $message
	 * @since Method available since Release 3.5.0
	 */
	public static function assertNotInternalType($expected, $actual, $message = '')
	{
		if(self::$_assert_type_compatability)
		{
			return self::assertNotType($expected, $actual, $message);
		}

		return self::assertNotInternalType($expected, $actual, $message);
	}

	/**
	 * Asserts that an attribute is of a given type.
	 *
	 * @param string $expected
	 * @param string $attributeName
	 * @param mixed  $classOrObject
	 * @param string $message
	 * @since Method available since Release 3.5.0
	 */
	public static function assertAttributeNotInternalType($expected, $attributeName, $classOrObject, $message = '')
	{
		if(self::$_assert_type_compatability)
		{
			return self::assertAttributeNotType($expected, $attributeName, $classOrObject, $message);
		}

		return self::assertAttributeNotInternalType($expected, $attributeName, $classOrObject, $message);
	}
}
