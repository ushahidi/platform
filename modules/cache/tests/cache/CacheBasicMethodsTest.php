<?php

/**
 * @package    Kohana/Cache
 * @group      kohana
 * @group      kohana.cache
 * @category   Test
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Kohana_CacheBasicMethodsTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var     Cache driver for this test
	 */
	protected $_cache_driver;

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
	}

	/**
	 * Accessor method to `$_cache_driver`. 
	 *
	 * @return  Cache
	 * @return  self
	 */
	public function cache(Cache $cache = NULL)
	{
		if ($cache === NULL)
			return $this->_cache_driver;

		$this->_cache_driver = $cache;
		return $this;
	}

	/**
	 * Data provider for test_set_get()
	 *
	 * @return  array
	 */
	public function provider_set_get()
	{
		$object = new StdClass;
		$object->foo = 'foo';
		$object->bar = 'bar';

		$html_text = <<<TESTTEXT
<!doctype html>  
<head> 
</head> 

<body>
</body>
</html>
TESTTEXT;

		return array(
			array(
				array(
					'id'      => 'string',    // Key to set to cache
					'value'   => 'foobar',    // Value to set to key
					'ttl'     => 0,           // Time to live
					'wait'    => FALSE,       // Test wait time to let cache expire
					'type'    => 'string',    // Type test
					'default' => NULL         // Default value get should return
				),
				'foobar'
			),
			array(
				array(
					'id'      => 'integer',
					'value'   => 101010,
					'ttl'     => 0,
					'wait'    => FALSE,
					'type'    => 'integer',
					'default' => NULL
				),
				101010
			),
			array(
				array(
					'id'      => 'float',
					'value'   => 10.00,
					'ttl'     => 0,
					'wait'    => FALSE,
					'type'    => 'float',
					'default' => NULL
				),
				10.00
			),
			array(
				array(
					'id'      => 'array',
					'value'   => array(
						'key'   => 'foo',
						'value' => 'bar'
					),
					'ttl'     => 0,
					'wait'    => FALSE,
					'type'    => 'array',
					'default' => NULL
				),
				array(
					'key'   => 'foo',
					'value' => 'bar'
				)
			),
			array(
				array(
					'id'      => 'boolean',
					'value'   => TRUE,
					'ttl'     => 0,
					'wait'    => FALSE,
					'type'    => 'boolean',
					'default' => NULL
				),
				TRUE
			),
			array(
				array(
					'id'      => 'null',
					'value'   => NULL,
					'ttl'     => 0,
					'wait'    => FALSE,
					'type'    => 'null',
					'default' => NULL
				),
				NULL
			),
			array(
				array(
					'id'      => 'object',
					'value'   => $object,
					'ttl'     => 0,
					'wait'    => FALSE,
					'type'    => 'object',
					'default' => NULL
				),
				$object
			),
			array(
				array(
					'id'      => 'bar\\ with / troublesome key',
					'value'   => 'foo bar snafu',
					'ttl'     => 0,
					'wait'    => FALSE,
					'type'    => 'string',
					'default' => NULL
				),
				'foo bar snafu'
			),
			array(
				array(
					'id'      => 'bar',
					'value'   => 'foo',
					'ttl'     => 3,
					'wait'    => 5,
					'type'    => 'null',
					'default' => NULL
				),
				NULL
			),
			array(
				array(
					'id'      => 'snafu',
					'value'   => 'fubar',
					'ttl'     => 3,
					'wait'    => 5,
					'type'    => 'string',
					'default' => 'something completely different!'
				),
				'something completely different!'
			),
			array(
				array(
					'id'      => 'new line test with HTML',
					'value'   => $html_text,
					'ttl'     => 10,
					'wait'    => FALSE,
					'type'    => 'string',
					'default' => NULL,
				),
				$html_text
			)
		);
	}

	/**
	 * Tests the [Cache::set()] method, testing;
	 * 
	 *  - The value is cached
	 *  - The lifetime is respected
	 *  - The returned value type is as expected
	 *  - The default not-found value is respected
	 * 
	 * @dataProvider provider_set_get
	 *
	 * @param   array    data 
	 * @param   mixed    expected 
	 * @return  void
	 */
	public function test_set_get(array $data, $expected)
	{
		$cache = $this->cache();
		extract($data);

		$this->assertTrue($cache->set($id, $value, $ttl));

		if ($wait !== FALSE)
		{
			// Lets let the cache expire
			sleep($wait);
		}

		$result = $cache->get($id, $default);
		$this->assertEquals($expected, $result);
		$this->assertInternalType($type, $result);

		unset($id, $value, $ttl, $wait, $type, $default);
	}

	/**
	 * Tests the [Cache::delete()] method, testing;
	 * 
	 *  - The a cached value is deleted from cache
	 *  - The cache returns a TRUE value upon deletion
	 *  - The cache returns a FALSE value if no value exists to delete
	 *
	 * @return  void
	 */
	public function test_delete()
	{
		// Init
		$cache = $this->cache();
		$cache->delete_all();

		// Test deletion if real cached value
		if ( ! $cache->set('test_delete_1', 'This should not be here!', 0))
		{
			$this->fail('Unable to set cache value to delete!');
		}

		// Test delete returns TRUE and check the value is gone
		$this->assertTrue($cache->delete('test_delete_1'));
		$this->assertNull($cache->get('test_delete_1'));

		// Test non-existant cache value returns FALSE if no error
		$this->assertFalse($cache->delete('test_delete_1'));
	}

	/**
	 * Tests [Cache::delete_all()] works as specified
	 *
	 * @return  void
	 * @uses    Kohana_CacheBasicMethodsTest::provider_set_get()
	 */
	public function test_delete_all()
	{
		// Init
		$cache = $this->cache();
		$data = $this->provider_set_get();

		foreach ($data as $key => $values)
		{
			extract($values[0]);
			if ( ! $cache->set($id, $value))
			{
				$this->fail('Unable to set: '.$key.' => '.$value.' to cache');
			}
			unset($id, $value, $ttl, $wait, $type, $default);
		}

		// Test delete_all is successful
		$this->assertTrue($cache->delete_all());

		foreach ($data as $key => $values)
		{
			// Verify data has been purged
			$this->assertSame('Cache Deleted!', $cache->get($values[0]['id'], 
				'Cache Deleted!'));
		}
	}

} // End Kohana_CacheBasicMethodsTest
