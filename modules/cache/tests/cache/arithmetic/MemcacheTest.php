<?php
include_once(Kohana::find_file('tests/cache/arithmetic', 'CacheArithmeticMethods'));

/**
 * @package    Kohana/Cache/Memcache
 * @group      kohana
 * @group      kohana.cache
 * @category   Test
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_CacheArithmeticMemcacheTest extends Kohana_CacheArithmeticMethodsTest {


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

		if ( ! extension_loaded('memcache'))
		{
			$this->markTestSkipped('Memcache PHP Extension is not available');
		}

		if ( ! $config = Kohana::$config->load('cache')->memcache)
		{
			$this->markTestSkipped('Unable to load Memcache configuration');
		}

		$memcache = new Memcache;
		if ( ! $memcache->connect($config['servers'][0]['host'], 
			$config['servers'][0]['port']))
		{
			$this->markTestSkipped('Unable to connect to memcache server @ '.
				$config['servers'][0]['host'].':'.
				$config['servers'][0]['port']);
		}

		if ($memcache->getVersion() === FALSE)
		{
			$this->markTestSkipped('Memcache server @ '.
				$config['servers'][0]['host'].':'.
				$config['servers'][0]['port'].
				' not responding!');
		}

		unset($memcache);

		$this->cache(Cache::instance('memcache'));
	}

	/**
	 * Tests that multiple values set with Memcache do not cause unexpected
	 * results. For accurate results, this should be run with a memcache
	 * configuration that includes multiple servers.
	 * 
	 * This is to test #4110
	 *
	 * @link    http://dev.kohanaframework.org/issues/4110
	 * @return  void
	 */
	public function test_multiple_set()
	{
		$cache = $this->cache();
		$id_set = 'set_id';
		$ttl = 300;

		$data = array(
			'foobar',
			0,
			1.0,
			new stdClass,
			array('foo', 'bar' => 1),
			TRUE,
			NULL,
			FALSE
		);

		$previous_set = $cache->get($id_set, NULL);

		foreach ($data as $value)
		{
			// Use Equals over Sames as Objects will not be equal
			$this->assertEquals($previous_set, $cache->get($id_set, NULL));
			$cache->set($id_set, $value, $ttl);

			$previous_set = $value;
		}
	}


} // End Kohana_CacheArithmeticMemcacheTest
