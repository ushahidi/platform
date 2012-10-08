<?php
include_once(Kohana::find_file('tests/cache', 'CacheBasicMethodsTest'));

/**
 * @package    Kohana/Cache
 * @group      kohana
 * @group      kohana.cache
 * @category   Test
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Cache_FileTest extends Kohana_CacheBasicMethodsTest {

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

		$this->cache(Cache::instance('file'));
	}

	/**
	 * Tests that ignored files are not removed from file cache
	 *
	 * @return  void
	 */
	public function test_ignore_delete_file()
	{
		$cache = $this->cache();
		$config = Kohana::$config->load('cache')->file;
		$file = $config['cache_dir'].'/.gitignore';

		// Lets pollute the cache folder
		file_put_contents($file, 'foobar');

		$this->assertTrue($cache->delete_all());
		$this->assertTrue(file_exists($file));
		$this->assertEquals('foobar', file_get_contents($file));

		unlink($file);
	}

	/**
	 * Provider for test_utf8
	 *
	 * @return  array
	 */
	public function provider_utf8()
	{
		return array(
			array(
				'This is â ütf-8 Ӝ☃ string',
				'This is â ütf-8 Ӝ☃ string'
			),
			array(
				'㆓㆕㆙㆛',
				'㆓㆕㆙㆛'
			),
			array(
				'அஆஇஈஊ',
				'அஆஇஈஊ'
			)
		);
	}

	/**
	 * Tests the file driver supports utf-8 strings
	 *
	 * @dataProvider provider_utf8
	 *
	 * @return  void
	 */
	public function test_utf8($input, $expected)
	{
		$cache = $this->cache();
		$cache->set('utf8', $input);

		$this->assertSame($expected, $cache->get('utf8'));
	}

} // End Kohana_SqliteTest
