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
class Kohana_CacheTest extends PHPUnit_Framework_TestCase {

	const BAD_GROUP_DEFINITION  = 1010;
	const EXPECT_SELF           = 1001;

	/**
	 * Data provider for test_instance
	 *
	 * @return  array
	 */
	public function provider_instance()
	{
		$tmp = realpath(sys_get_temp_dir());

		return array(
			// Test default group
			array(
				NULL,
				Cache::instance('file')
			),
			// Test defined group
			array(
				'file',
				Cache::instance('file')
			),
			// Test bad group definition
			array(
				Kohana_CacheTest::BAD_GROUP_DEFINITION,
				'Failed to load Kohana Cache group: 1010'
			),
		);
	}

	/**
	 * Tests the [Cache::factory()] method behaves as expected
	 * 
	 * @dataProvider provider_instance
	 *
	 * @return  void
	 */
	public function test_instance($group, $expected)
	{
		if (in_array($group, array(
			Kohana_CacheTest::BAD_GROUP_DEFINITION,
			)
		))
		{
			$this->setExpectedException('Cache_Exception');
		}

		try
		{
			$cache = Cache::instance($group);
		}
		catch (Cache_Exception $e)
		{
			$this->assertSame($expected, $e->getMessage());
			throw $e;
		}

		$this->assertInstanceOf(get_class($expected), $cache);
		$this->assertSame($expected->config(), $cache->config());
	}

	/**
	 * Tests that `clone($cache)` will be prevented to maintain singleton
	 *
	 * @return  void
	 * @expectedException Cache_Exception
	 */
	public function test_cloning_fails()
	{
		try
		{
			$cache_clone = clone(Cache::instance('file'));
		}
		catch (Cache_Exception $e)
		{
			$this->assertSame('Cloning of Kohana_Cache objects is forbidden', 
				$e->getMessage());
			throw $e;
		}
	}

	/**
	 * Data provider for test_config
	 *
	 * @return  array
	 */
	public function provider_config()
	{
		return array(
			array(
				array(
					'server'     => 'otherhost',
					'port'       => 5555,
					'persistent' => TRUE,
				),
				NULL,
				Kohana_CacheTest::EXPECT_SELF,
				array(
					'server'     => 'otherhost',
					'port'       => 5555,
					'persistent' => TRUE,
				),
			),
			array(
				'foo',
				'bar',
				Kohana_CacheTest::EXPECT_SELF,
				array(
					'foo'        => 'bar'
				)
			),
			array(
				'server',
				NULL,
				NULL,
				array()
			),
			array(
				NULL,
				NULL,
				array(),
				array()
			)
		);
	}

	/**
	 * Tests the config method behaviour
	 * 
	 * @dataProvider provider_config
	 *
	 * @param   mixed    key value to set or get
	 * @param   mixed    value to set to key
	 * @param   mixed    expected result from [Cache::config()]
	 * @param   array    expected config within cache
	 * @return  void
	 */
	public function test_config($key, $value, $expected_result, array $expected_config)
	{
		$cache = $this->getMock('Cache_File', NULL, array(), '', FALSE);

		if ($expected_result === Kohana_CacheTest::EXPECT_SELF)
		{
			$expected_result = $cache;
		}

		$this->assertSame($expected_result, $cache->config($key, $value));
		$this->assertSame($expected_config, $cache->config());
	}

	/**
	 * Data provider for test_sanitize_id
	 *
	 * @return  array
	 */
	public function provider_sanitize_id()
	{
		return array(
			array(
				'foo',
				'foo'
			),
			array(
				'foo+-!@',
				'foo+-!@'
			),
			array(
				'foo/bar',
				'foo_bar',
			),
			array(
				'foo\\bar',
				'foo_bar'
			),
			array(
				'foo bar',
				'foo_bar'
			),
			array(
				'foo\\bar snafu/stfu',
				'foo_bar_snafu_stfu'
			)
		);
	}

	/**
	 * Tests the [Cache::_sanitize_id()] method works as expected.
	 * This uses some nasty reflection techniques to access a protected
	 * method.
	 * 
	 * @dataProvider provider_sanitize_id
	 *
	 * @param   string    id 
	 * @param   string    expected 
	 * @return  void
	 */
	public function test_sanitize_id($id, $expected)
	{
		$cache = $this->getMock('Cache', array(
			'get',
			'set',
			'delete',
			'delete_all'
			), array(array()),
			'', FALSE
		);

		$cache_reflection = new ReflectionClass($cache);
		$sanitize_id = $cache_reflection->getMethod('_sanitize_id');
		$sanitize_id->setAccessible(TRUE);

		$this->assertSame($expected, $sanitize_id->invoke($cache, $id));
	}
} // End Kohana_CacheTest
