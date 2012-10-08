<?php
include_once(Kohana::find_file('tests/cache/arithmetic', 'CacheArithmeticMethods'));

/**
 * @package    Kohana/Cache
 * @group      kohana
 * @group      kohana.cache
 * @category   Test
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_ApcTest extends Kohana_CacheArithmeticMethodsTest {

	/**
	 * This method MUST be implemented by each driver to setup the `Cache`
	 * instance for each test.
	 * 
	 * This method should do the following tasks for each driver test:
	 * 
	 *  - Test the Cache instance driver is available, skip test otherwise
	 *  - Setup the Cache instance
	 *  - Call the parent setup method, `parent::setUp()`
	 *
	 * @return  void
	 */
	public function setUp()
	{
		parent::setUp();

		if ( ! extension_loaded('apc'))
		{
			$this->markTestSkipped('APC PHP Extension is not available');
		}

		if (ini_get('apc.enable_cli') != '1')
		{
			$this->markTestSkipped('Unable to test APC in CLI mode. To fix '.
				'place "apc.enable_cli=1" in your php.ini file');
		}

		$this->cache(Cache::instance('apc'));
	}

	/**
	 * Tests the [Cache::set()] method, testing;
	 * 
	 *  - The value is cached
	 *  - The lifetime is respected
	 *  - The returned value type is as expected
	 *  - The default not-found value is respected
	 * 
	 * This test doesn't test the TTL as there is a known bug/feature
	 * in APC that prevents the same request from killing cache on timeout.
	 * 
	 * @link   http://pecl.php.net/bugs/bug.php?id=16814
	 * 
	 * @dataProvider provider_set_get
	 *
	 * @param   array    data 
	 * @param   mixed    expected 
	 * @return  void
	 */
	public function test_set_get(array $data, $expected)
	{
		if ($data['wait'] !== FALSE)
		{
			$this->markTestSkipped('Unable to perform TTL test in CLI, see: '.
				'http://pecl.php.net/bugs/bug.php?id=16814 for more info!');
		}

		parent::test_set_get($data, $expected);
	}

} // End Kohana_ApcTest
