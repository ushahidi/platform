<?php
include_once(Kohana::find_file('tests/cache', 'CacheBasicMethodsTest'));

/**
 * @package    Kohana/Cache/Memcache
 * @group      kohana
 * @group      kohana.cache
 * @category   Test
 * @author     Kohana Team
 * @copyright  (c) 2009-2012 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Kohana_CacheArithmeticMethodsTest extends Kohana_CacheBasicMethodsTest {

	public function tearDown()
	{
		parent::tearDown();

		// Cleanup
		$cache = $this->cache();

		if ($cache instanceof Cache)
		{
			$cache->delete_all();
		}
	}

	/**
	 * Provider for test_increment
	 *
	 * @return  array
	 */
	public function provider_increment()
	{
		return array(
			array(
				0,
				array(
					'id'    => 'increment_test_1',
					'step'  => 1
				),
				1
			),
			array(
				1,
				array(
					'id'    => 'increment_test_2',
					'step'  => 1
				),
				2
			),
			array(
				5,
				array(
					'id'    => 'increment_test_3',
					'step'  => 5
				),
				10
			),
			array(
				NULL,
				array(
					'id'    => 'increment_test_4',
					'step'  => 1
				),
				FALSE
			),
		);
	}

	/**
	 * Test for [Cache_Arithmetic::increment()]
	 * 
	 * @dataProvider provider_increment
	 *
	 * @param   integer  start state
	 * @param   array    increment arguments
	 * @return  void
	 */
	public function test_increment(
		$start_state = NULL,
		array $inc_args,
		$expected)
	{
		$cache = $this->cache();

		if ($start_state !== NULL)
		{
			$cache->set($inc_args['id'], $start_state, 0);
		}

		$this->assertSame(
			$expected,
			$cache->increment(
				$inc_args['id'],
				$inc_args['step']
			)
		);
	}

	/**
	 * Provider for test_decrement
	 *
	 * @return  array
	 */
	public function provider_decrement()
	{
		return array(
			array(
				10,
				array(
					'id'    => 'decrement_test_1',
					'step'  => 1
				),
				9
			),
			array(
				10,
				array(
					'id'    => 'decrement_test_2',
					'step'  => 2
				),
				8
			),
			array(
				50,
				array(
					'id'    => 'decrement_test_3',
					'step'  => 5
				),
				45
			),
			array(
				NULL,
				array(
					'id'    => 'decrement_test_4',
					'step'  => 1
				),
				FALSE
			),
		);	}

	/**
	 * Test for [Cache_Arithmetic::decrement()]
	 * 
	 * @dataProvider provider_decrement
	 *
	 * @param   integer  start state
	 * @param   array    decrement arguments
	 * @return  void
	 */
	public function test_decrement(
		$start_state = NULL,
		array $dec_args,
		$expected)
	{
		$cache = $this->cache();

		if ($start_state !== NULL)
		{
			$cache->set($dec_args['id'], $start_state, 0);
		}

		$this->assertSame(
			$expected,
			$cache->decrement(
				$dec_args['id'],
				$dec_args['step']
			)
		);
	}

} // End Kohana_CacheArithmeticMethodsTest
