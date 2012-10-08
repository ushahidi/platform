<?php defined('SYSPATH') or die('No direct script access.');

/**
 * PHPUnit testsuite for kohana application
 *
 * @package    Kohana/UnitTest
 * @author     Kohana Team
 * @author     BRMatt <matthew@sigswitch.com>
 * @author	   Paul Banks
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Unittest_Tests {
	static protected $cache = array();

	/**
	 * Flag to identify whether the installed version of phpunit
	 * is greater than or equal to 3.5
	 * @var boolean
	 */
	static protected $phpunit_v35 = FALSE;

	/**
	 * Loads test files if they cannot be found by kohana
	 * @param <type> $class
	 */
	static function autoload($class)
	{
		$file = str_replace('_', '/', $class);

		if ($file = Kohana::find_file('tests', $file))
		{
			require_once $file;
		}
	}

	/**
	 * Configures the environment for testing
	 *
	 * Does the following:
	 *
	 * * Loads the phpunit framework (for the web ui)
	 * * Restores exception phpunit error handlers (for cli)
	 * * registeres an autoloader to load test files
	 */
	static public function configure_environment($do_whitelist = TRUE, $do_blacklist = TRUE)
	{
		restore_exception_handler();
		restore_error_handler();

		spl_autoload_register(array('Unittest_tests', 'autoload'));

		// As of PHPUnit v3.5 there are slight differences in the way files are black|whitelisted
		self::$phpunit_v35 = function_exists('phpunit_autoload');

		Unittest_tests::$cache = (($cache = Kohana::cache('unittest_whitelist_cache')) === NULL) ? array() : $cache;

	}

	/**
	 * Creates the test suite for kohana
	 *
	 * @return Unittest_TestSuite
	 */
	static function suite()
	{
		static $suite = NULL;

		if ($suite instanceof PHPUnit_Framework_TestSuite)
		{
			return $suite;
		}

		Unittest_Tests::configure_environment();

		$suite = new Unittest_TestSuite;
		
		// Load the whitelist and blacklist for code coverage		
		$config = Kohana::$config->load('unittest');
		
		if ($config->use_whitelist)
		{
			$files = Unittest_Tests::whitelist(NULL, $suite);
		}
		
		if (count($config['blacklist']))
		{
			Unittest_Tests::blacklist($config->blacklist, $suite);
		}

		// Add tests
		$files = Kohana::list_files('tests');
		$config = Kohana::$config->load('unittest');
		foreach($config->test_blacklist as $bl)
		{
			unset($files[$bl]);
		}

		self::addTests($suite, $files);

		return $suite;
	}

	/**
	 * Add files to test suite $suite
	 *
	 * Uses recursion to scan subdirectories
	 *
	 * @param Unittest_TestSuite  $suite   The test suite to add to
	 * @param array                        $files   Array of files to test
	 */
	static function addTests(Unittest_TestSuite $suite, array $files)
	{

		foreach ($files as $path => $file)
		{
			if (is_array($file))
			{
				if ($path != 'tests'.DIRECTORY_SEPARATOR.'test_data')
				{					
					self::addTests($suite, $file);
				}
			}
			else
			{
				// Make sure we only include php files
				if (is_file($file) AND substr($file, -strlen(EXT)) === EXT)
				{
					// The default PHPUnit TestCase extension
					if ( ! strpos($file, 'TestCase'.EXT))
					{
						$suite->addTestFile($file);
					}
					else
					{
						require_once($file);
					}

					if (self::$phpunit_v35)
					{
						$suite->addFileToBlacklist($file);
					}
					else
					{
						PHPUnit_Util_Filter::addFileToFilter($file);
					}
				}
			}
		}
	}

	/**
	 * Blacklist a set of files in PHPUnit code coverage
	 *
	 * @param array $blacklist_items A set of files to blacklist
	 * @param Unittest_TestSuite $suite The test suite
	 */
	static public function blacklist(array $blacklist_items, Unittest_TestSuite $suite = NULL)
	{
		if (self::$phpunit_v35)
		{
			foreach ($blacklist_items as $item)
			{
				if (is_dir($item))
				{
					$suite->addDirectoryToBlacklist($item);
				}
				else
				{
					$suite->addFileToBlacklist($item);
				}
			}
		}
		else
		{
			foreach ($blacklist_items as $item)
			{
				if (is_dir($item))
				{
					PHPUnit_Util_Filter::addDirectoryToFilter($item);
				}
				else
				{
					PHPUnit_Util_Filter::addFileToFilter($item);
				}
			}
		}
	}

	/**
	 * Sets the whitelist
	 *
	 * If no directories are provided then the function'll load the whitelist
	 * set in the config file
	 *
	 * @param array $directories Optional directories to whitelist
	 * @param Unittest_Testsuite $suite Suite to load the whitelist into
	 */
	static public function whitelist(array $directories = NULL, Unittest_TestSuite $suite = NULL)
	{
		if (empty($directories))
		{
			$directories = self::get_config_whitelist();
		}
		if (count($directories))
		{
			foreach ($directories as & $directory)
			{
				$directory = realpath($directory).'/';
			}

			// Only whitelist the "top" files in the cascading filesystem
			self::set_whitelist(Kohana::list_files('classes', $directories), $suite);
		}

		return $directories;
	}

	/**
	 * Works out the whitelist from the config
	 * Used only on the CLI
	 *
	 * @returns array Array of directories to whitelist
	 */
	static protected function get_config_whitelist()
	{
		$config = Kohana::$config->load('unittest');
		$directories = array();

		if ($config->whitelist['app'])
		{
			$directories['k_app'] = APPPATH;
		}

		if ($modules = $config->whitelist['modules'])
		{
			$k_modules = Kohana::modules();

			// Have to do this because kohana merges config...
			// If you want to include all modules & override defaults then TRUE must be the first
			// value in the modules array of your app/config/unittest file
			if (array_search(TRUE, $modules, TRUE) === (count($modules) - 1))
			{
				$modules = $k_modules;
			}
			elseif (array_search(FALSE, $modules, TRUE) === FALSE)
			{
				$modules = array_intersect_key($k_modules, array_combine($modules, $modules));
			}
			else
			{
				// modules are disabled
				$modules = array();
			}

			$directories += $modules;
		}

		if ($config->whitelist['system'])
		{
			$directories['k_sys'] = SYSPATH;
		}

		return $directories;
	}

	/**
	 * Recursively whitelists an array of files
	 *
	 * @param array $files Array of files to whitelist
	 * @param Unittest_TestSuite $suite Suite to load the whitelist into
	 */
	static protected function set_whitelist($files, Unittest_TestSuite $suite = NULL)
	{

		foreach ($files as $file)
		{
			if (is_array($file))
			{
				self::set_whitelist($file, $suite);
			}
			else
			{
				if ( ! isset(Unittest_tests::$cache[$file]))
				{
					$relative_path = substr($file, strrpos($file, 'classes'.DIRECTORY_SEPARATOR) + 8, -strlen(EXT));
					$cascading_file = Kohana::find_file('classes', $relative_path);

					// The theory is that if this file is the highest one in the cascading filesystem
					// then it's safe to whitelist
					Unittest_tests::$cache[$file] =  ($cascading_file === $file);
				}

				if (Unittest_tests::$cache[$file])
				{
					if (isset($suite))
					{
						$suite->addFileToWhitelist($file);
					}
					else
					{
						PHPUnit_Util_Filter::addFileToWhitelist($file);
					}
				}
			}
		}
	}
}
