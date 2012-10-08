<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');

/**
 * Tests the Arr lib that's shipped with kohana
 *
 * @group kohana
 * @group kohana.core
 * @group kohana.core.arr
 *
 * @package    Kohana
 * @category   Tests
 * @author     Kohana Team
 * @author     BRMatt <matthew@sigswitch.com>
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Kohana_ArrTest extends Unittest_TestCase
{
	/**
	 * Provides test data for test_callback()
	 *
	 * @return array
	 */
	public function provider_callback()
	{
		return array(
			// Tests....
			// That no parameters returns null
			array('function', array('function', NULL)),
			// That we can get an array of parameters values
			array('function(1,2,3)', array('function', array('1', '2', '3'))),
			// That it's not just using the callback "function"
			array('different_name(harry,jerry)', array('different_name', array('harry', 'jerry'))),
			// That static callbacks are parsed into arrays
			array('kohana::appify(this)', array(array('kohana', 'appify'), array('this'))),
			// Spaces are preserved in parameters
			array('deal::make(me, my mate )', array(array('deal', 'make'), array('me', ' my mate ')))
			// TODO: add more cases
		);
	}

	/**
	 * Tests Arr::callback()
	 *
	 * @test
	 * @dataProvider provider_callback
	 * @param string $str       String to parse
	 * @param array  $expected  Callback and its parameters
	 */
	public function test_callback($str, $expected)
	{
		$result = Arr::callback($str);

		$this->assertSame(2, count($result));
		$this->assertSame($expected, $result);
	}

	/**
	 * Provides test data for test_extract
	 *
	 * @return array
	 */
	public function provider_extract()
	{
		return array(
			array(
				array('kohana' => 'awesome', 'blueflame' => 'was'),
				array('kohana', 'cakephp', 'symfony'),
				NULL,
				array('kohana' => 'awesome', 'cakephp' => NULL, 'symfony' => NULL)
			),
			// I realise noone should EVER code like this in real life,
			// but unit testing is very very very very boring
			array(
				array('chocolate cake' => 'in stock', 'carrot cake' => 'in stock'),
				array('carrot cake', 'humble pie'),
				'not in stock',
				array('carrot cake' => 'in stock', 'humble pie' => 'not in stock'),
			),
		);
	}

	/**
	 * Tests Arr::extract()
	 *
	 * @test
	 * @dataProvider provider_extract
	 * @param array $array
	 * @param array $keys
	 * @param mixed $default
	 * @param array $expected
	 */
	public function test_extract(array $array, array $keys, $default, $expected)
	{
		$array = Arr::extract($array, $keys, $default);

		$this->assertSame(count($expected), count($array));
		$this->assertSame($expected, $array);
	}

	/**
	 * Provides test data for test_pluck
	 *
	 * @return array
	 */
	public function provider_pluck()
	{
		return array(
			array(
				array(
					  array('id' => 20, 'name' => 'John Smith'),
					  array('name' => 'Linda'),
					  array('id' => 25, 'name' => 'Fred'),
					 ),
				'id',
				array(20, 25)
			),
		);
	}

	/**
	 * Tests Arr::pluck()
	 *
	 * @test
	 * @dataProvider provider_pluck
	 * @param array $array
	 * @param string $key
	 * @param array $expected
	 */
	public function test_pluck(array $array, $key, $expected)
	{
		$array = Arr::pluck($array, $key);

		$this->assertSame(count($expected), count($array));
		$this->assertSame($expected, $array);
	}

	/**
	 * Provides test data for test_get()
	 *
	 * @return array
	 */
	public function provider_get()
	{
		return array(
			array(array('uno', 'dos', 'tress'), 1, NULL, 'dos'),
			array(array('we' => 'can', 'make' => 'change'), 'we', NULL, 'can'),

			array(array('uno', 'dos', 'tress'), 10, NULL, NULL),
			array(array('we' => 'can', 'make' => 'change'), 'he', NULL, NULL),
			array(array('we' => 'can', 'make' => 'change'), 'he', 'who', 'who'),
			array(array('we' => 'can', 'make' => 'change'), 'he', array('arrays'), array('arrays')),
		);
	}

	/**
	 * Tests Arr::get()
	 *
	 * @test
	 * @dataProvider provider_get()
	 * @param array          $array      Array to look in
	 * @param string|integer $key        Key to look for
	 * @param mixed          $default    What to return if $key isn't set
	 * @param mixed          $expected   The expected value returned
	 */
	public function test_get(array $array, $key, $default, $expected)
	{
		$this->assertSame(
			$expected,
			Arr::get($array, $key, $default)
		);
	}

	/**
	 * Provides test data for test_is_assoc()
	 *
	 * @return array
	 */
	public function provider_is_assoc()
	{
		return array(
			array(array('one', 'two', 'three'), FALSE),
			array(array('one' => 'o clock', 'two' => 'o clock', 'three' => 'o clock'), TRUE),
		);
	}

	/**
	 * Tests Arr::is_assoc()
	 *
	 * @test
	 * @dataProvider provider_is_assoc
	 * @param array   $array     Array to check
	 * @param boolean $expected  Is $array assoc
	 */
	public function test_is_assoc(array $array, $expected)
	{
		$this->assertSame(
			$expected,
			Arr::is_assoc($array)
		);
	}

	/**
	 * Provides test data for test_is_array()
	 *
	 * @return array
	 */
	public function provider_is_array()
	{
		return array(
			array($a = array('one', 'two', 'three'), TRUE),
			array(new ArrayObject($a), TRUE),
			array(new ArrayIterator($a), TRUE),
			array('not an array', FALSE),
			array(new stdClass, FALSE),
		);
	}

	/**
	 * Tests Arr::is_array()
	 *
	 * @test
	 * @dataProvider provider_is_array
	 * @param mixed   $value     Value to check
	 * @param boolean $expected  Is $value an array?
	 */
	public function test_is_array($array, $expected)
	{
		$this->assertSame(
			$expected,
			Arr::is_array($array)
		);
	}

	public function provider_merge()
	{
		return array(
			// Test how it merges arrays and sub arrays with assoc keys
			array(
				array('name' => 'mary', 'children' => array('fred', 'paul', 'sally', 'jane')),
				array('name' => 'john', 'children' => array('fred', 'paul', 'sally', 'jane')),
				array('name' => 'mary', 'children' => array('jane')),
			),
			// See how it merges sub-arrays with numerical indexes
			array(
				array(array('test1','test3'), array('test2','test4')),
				array(array('test1'), array('test2')),
				array(array('test3'), array('test4')),
			),
			array(
				array(array('test1','test3'), array('test2','test4')),
				array(array('test1'), array('test2')),
				array(array('test3'), array('test4')),
			),
			array(
				array('digits' => array(0, 1, 2, 3)),
				array('digits' => array(0, 1)),
				array('digits' => array(2, 3)),
			),
			// See how it manages merging items with numerical indexes
			array(
				array(0, 1, 2, 3),
				array(0, 1),
				array(2, 3),
			),
			// Try and get it to merge assoc. arrays recursively
			array(
				array('foo' => 'bar', array('temp' => 'life')),
				array('foo' => 'bin', array('temp' => 'name')),
				array('foo' => 'bar', array('temp' => 'life')),
			),
			// Bug #3139
			array(
				array('foo'	=> array('bar')),
				array('foo'	=> 'bar'),
				array('foo'	=> array('bar')),
			),
			array(
				array('foo'	=> 'bar'),
				array('foo'	=> array('bar')),
				array('foo'	=> 'bar'),
			),
		);
	}

	/**
	 *
	 * @test
	 * @dataProvider provider_merge
	 */
	public function test_merge($expected, $array1, $array2)
	{
		$this->assertSame(
			$expected,
			Arr::merge($array1,$array2)
		);
	}

	/**
	 * Provides test data for test_path()
	 *
	 * @return array
	 */
	public function provider_path()
	{
		$array = array(
			'foobar' => array('definition' => 'lost'),
			'kohana' => 'awesome',
			'users'  => array(
				1 => array('name' => 'matt'),
				2 => array('name' => 'john', 'interests' => array('hocky' => array('length' => 2), 'football' => array())),
				3 => 'frank', // Issue #3194
			),
			'object' => new ArrayObject(array('iterator' => TRUE)), // Iterable object should work exactly the same
		);

		return array(
			// Tests returns normal values
			array($array['foobar'], $array, 'foobar'),
			array($array['kohana'], $array, 'kohana'),
			array($array['foobar']['definition'], $array, 'foobar.definition'),
			// Custom delimiters
			array($array['foobar']['definition'], $array, 'foobar/definition', NULL, '/'),
			// We should be able to use NULL as a default, returned if the key DNX
			array(NULL, $array, 'foobar.alternatives',  NULL),
			array(NULL, $array, 'kohana.alternatives',  NULL),
			// Try using a string as a default
			array('nothing', $array, 'kohana.alternatives',  'nothing'),
			// Make sure you can use arrays as defaults
			array(array('far', 'wide'), $array, 'cheese.origins',  array('far', 'wide')),
			// Ensures path() casts ints to actual integers for keys
			array($array['users'][1]['name'], $array, 'users.1.name'),
			// Test that a wildcard returns the entire array at that "level"
			array($array['users'], $array, 'users.*'),
			// Now we check that keys after a wilcard will be processed
			array(array(0 => array(0 => 2)), $array, 'users.*.interests.*.length'),
			// See what happens when it can't dig any deeper from a wildcard
			array(NULL, $array, 'users.*.fans'),
			// Starting wildcards, issue #3269
			array(array('matt', 'john'), $array['users'], '*.name'),
			// Path as array, issue #3260
			array($array['users'][2]['name'], $array, array('users', 2, 'name')),
			array($array['object']['iterator'], $array, 'object.iterator'),
		);
	}

	/**
	 * Tests Arr::path()
	 *
	 * @test
	 * @dataProvider provider_path
	 * @param string  $path       The path to follow
	 * @param mixed   $default    The value to return if dnx
	 * @param boolean $expected   The expected value
	 * @param string  $delimiter  The path delimiter
	 */
	public function test_path($expected, $array, $path, $default = NULL, $delimiter = NULL)
	{
		$this->assertSame(
			$expected,
			Arr::path($array, $path, $default, $delimiter)
		);
	}

	/**
	 * Provides test data for test_path()
	 *
	 * @return array
	 */
	public function provider_set_path()
	{
		return array(
			// Tests returns normal values
			array(array('foo' => 'bar'), array(), 'foo', 'bar'),
			array(array('kohana' => array('is' => 'awesome')), array(), 'kohana.is', 'awesome'),
			array(array('kohana' => array('is' => 'cool', 'and' => 'slow')),
				  array('kohana' => array('is' => 'cool')), 'kohana.and', 'slow'),
			// Custom delimiters
			array(array('kohana' => array('is' => 'awesome')), array(), 'kohana/is', 'awesome', '/'),
			// Ensures set_path() casts ints to actual integers for keys
			array(array('foo' => array('bar')), array('foo' => array('test')), 'foo.0', 'bar'),
		);
	}

	/**
	 * Tests Arr::path()
	 *
	 * @test
	 * @dataProvider provider_set_path
	 * @param string  $path       The path to follow
	 * @param boolean $expected   The expected value
	 * @param string  $delimiter  The path delimiter
	 */
	public function test_set_path($expected, $array, $path, $value, $delimiter = NULL)
	{
		Arr::set_path($array, $path, $value, $delimiter);

		$this->assertSame($expected, $array);
	}

	/**
	 * Provides test data for test_range()
	 *
	 * @return array
	 */
	public function provider_range()
	{
		return array(
			array(1, 2),
			array(1, 100),
			array(25, 10),
		);
	}

	/**
	 * Tests Arr::range()
	 *
	 * @dataProvider provider_range
	 * @param integer $step  The step between each value in the array
	 * @param integer $max   The max value of the range (inclusive)
	 */
	public function test_range($step, $max)
	{
		$range = Arr::range($step, $max);

		$this->assertSame((int) floor($max / $step), count($range));

		$current = $step;

		foreach($range as $key => $value)
		{
			$this->assertSame($key, $value);
			$this->assertSame($current, $key);
			$this->assertLessThanOrEqual($max, $key);
			$current += $step;
		}
	}

	/**
	 * Provides test data for test_unshift()
	 *
	 * @return array
	 */
	public function provider_unshift()
	{
		return array(
			array(array('one' => '1', 'two' => '2',), 'zero', '0'),
			array(array('step 1', 'step 2', 'step 3'), 'step 0', 'wow')
		);
	}

	/**
	 * Tests Arr::unshift()
	 *
	 * @test
	 * @dataProvider provider_unshift
	 * @param array $array
	 * @param string $key
	 * @param mixed $value
	 */
	public function test_unshift(array $array, $key, $value)
	{
		$original = $array;

		Arr::unshift($array, $key, $value);

		$this->assertNotSame($original, $array);
		$this->assertSame(count($original) + 1, count($array));
		$this->assertArrayHasKey($key, $array);

		$this->assertSame($value, reset($array));
		$this->assertSame(key($array), $key);
	}

	/**
	 * Provies test data for test_overwrite
	 *
	 * @return array Test Data
	 */
	public function provider_overwrite()
	{
		return array(
			array(
				array('name' => 'Henry', 'mood' => 'tired', 'food' => 'waffles', 'sport' => 'checkers'),
				array('name' => 'John', 'mood' => 'bored', 'food' => 'bacon', 'sport' => 'checkers'),
				array('name' => 'Matt', 'mood' => 'tired', 'food' => 'waffles'),
				array('name' => 'Henry', 'age' => 18,),
			),
		);
	}

	/**
	 *
	 * @test
	 * @dataProvider provider_overwrite
	 */
	public function test_overwrite($expected, $arr1, $arr2, $arr3 = array(), $arr4 = array())
	{
		$this->assertSame(
			$expected,
			Arr::overwrite($arr1, $arr2, $arr3, $arr4)
		);
	}

	/**
	 * Provides test data for test_map
	 *
	 * @return array Test Data
	 */
	public function provider_map()
	{
		return array(
			array('strip_tags', array('<p>foobar</p>'), NULL, array('foobar')),
			array('strip_tags', array(array('<p>foobar</p>'), array('<p>foobar</p>')), NULL, array(array('foobar'), array('foobar'))),
			array(
				'strip_tags',
				array(
					'foo' => '<p>foobar</p>',
					'bar' => '<p>foobar</p>',
				),
				NULL,
				array(
					'foo' => 'foobar',
					'bar' => 'foobar',
				),
			),
			array(
				'strip_tags',
				array(
					'foo' => '<p>foobar</p>',
					'bar' => '<p>foobar</p>',
				),
				array('foo'),
				array(
					'foo' => 'foobar',
					'bar' => '<p>foobar</p>',
				),
			),
			array(
				array(
					'strip_tags',
					'trim',
				),
				array(
					'foo' => '<p>foobar </p>',
					'bar' => '<p>foobar</p>',
				),
				NULL,
				array(
					'foo' => 'foobar',
					'bar' => 'foobar',
				),
			),
		);
	}

	/**
	 *
	 * @test
	 * @dataProvider provider_map
	 */
	public function test_map($method, $source, $keys, $expected)
	{
		$this->assertSame(
			$expected,
			Arr::map($method, $source, $keys)
		);
	}

	/**
	 * Provides test data for test_flatten
	 *
	 * @return array Test Data
	 */
	public function provider_flatten()
	{
		return array(
			array(array('set' => array('one' => 'something'), 'two' => 'other'), array('one' => 'something', 'two' => 'other')),
		);
	}

	/**
	 *
	 * @test
	 * @dataProvider provider_flatten
	 */
	public function test_flatten($source, $expected)
	{
		$this->assertSame(
			$expected,
			Arr::flatten($source)
		);
	}
}
