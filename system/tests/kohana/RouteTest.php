<?php defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');

/**
 * Description of RouteTest
 *
 * @group kohana
 * @group kohana.core
 * @group kohana.core.route
 *
 * @package    Kohana
 * @category   Tests
 * @author     Kohana Team
 * @author     BRMatt <matthew@sigswitch.com>
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */

include Kohana::find_file('tests', 'test_data/callback_routes');

class Kohana_RouteTest extends Unittest_TestCase
{
	/**
	 * Remove all caches
	 */
	public function setUp()
	{
		parent::setUp();

		$this->cleanCacheDir();
	}

	/**
	 * Removes cache files created during tests
	 */
	public function tearDown()
	{
		parent::tearDown();

		$this->cleanCacheDir();
	}

	/**
	 * If Route::get() is asked for a route that does not exist then
	 * it should throw a Kohana_Exception
	 *
	 * Note use of @expectedException
	 *
	 * @test
	 * @covers Route::get
	 * @expectedException Kohana_Exception
	 */
	public function test_get_throws_exception_if_route_dnx()
	{
		Route::get('HAHAHAHAHAHAHAHAHA');
	}

	/**
	 * Route::all() should return all routes defined via Route::set()
	 * and not through new Route()
	 *
	 * @test
	 * @covers Route::all
	 */
	public function test_all_returns_all_defined_routes()
	{
		$defined_routes = self::readAttribute('Route', '_routes');

		$this->assertSame($defined_routes, Route::all());
	}

	/**
	 * Route::name() should fetch the name of a passed route
	 * If route is not found then it should return FALSE
	 *
	 * @TODO: This test needs to segregate the Route::$_routes singleton
	 * @test
	 * @covers Route::name
	 */
	public function test_name_returns_routes_name_or_false_if_dnx()
	{
		$route = Route::set('flamingo_people', 'flamingo/dance');

		$this->assertSame('flamingo_people', Route::name($route));

		$route = new Route('dance/dance');

		$this->assertFalse(Route::name($route));
	}

	/**
	 * If Route::cache() was able to restore routes from the cache then
	 * it should return TRUE and load the cached routes
	 *
	 * @test
	 * @covers Route::cache
	 */
	public function test_cache_stores_route_objects()
	{
		$routes = Route::all();

		// First we create the cache
		Route::cache(TRUE);

		// Now lets modify the "current" routes
		Route::set('nonsensical_route', 'flabbadaga/ding_dong');

		// Then try and load said cache
		$this->assertTrue(Route::cache());

		// Check the route cache flag
		$this->assertTrue(Route::$cache);

		// And if all went ok the nonsensical route should be gone...
		$this->assertEquals($routes, Route::all());
	}

	/**
	 * Route::cache() should return FALSE if cached routes could not be found
	 *
	 * The cache is cleared before and after each test in setUp tearDown
	 * by cleanCacheDir()
	 *
	 * @test
	 * @covers Route::cache
	 */
	public function test_cache_returns_false_if_cache_dnx()
	{
		$this->assertSame(FALSE, Route::cache(), 'Route cache was not empty');

		// Check the route cache flag
		$this->assertFalse(Route::$cache);
	}

	/**
	 * If the constructor is passed a NULL uri then it should assume it's
	 * being loaded from the cache & therefore shouldn't override the cached attributes
	 *
	 * @test
	 * @covers Route::__construct
	 */
	public function test_constructor_returns_if_uri_is_null()
	{
		// We use a mock object to make sure that the route wasn't recompiled
		$route = $this->getMock('Route', array('_compile'), array(), '', FALSE);

		$route
			->expects($this->never())
			->method('_compile');

		$route->__construct(NULL,NULL);

		$this->assertAttributeSame('', '_uri', $route);
		$this->assertAttributeSame(array(), '_regex', $route);
		$this->assertAttributeSame(array('action' => 'index', 'host' => FALSE), '_defaults', $route);
		$this->assertAttributeSame(NULL, '_route_regex', $route);
	}

	/**
	 * Provider for test_constructor_only_changes_custom_regex_if_passed
	 *
	 * @return array
	 */
	public function provider_constructor_only_changes_custom_regex_if_passed()
	{
		return array(
			array('<controller>/<action>', '<controller>/<action>'),
			array(array('Route_Holder', 'default_callback'), array('Route_Holder', 'default_callback')),
		);
	}

	/**
	 * The constructor should only use custom regex if passed a non-empty array
	 *
	 * Technically we can't "test" this as the default regex is an empty array, this
	 * is purely for improving test coverage
	 *
	 * @dataProvider provider_constructor_only_changes_custom_regex_if_passed
	 *
	 * @test
	 * @covers Route::__construct
	 */
	public function test_constructor_only_changes_custom_regex_if_passed($uri, $uri2)
	{
		$route = new Route($uri, array());

		$this->assertAttributeSame(array(), '_regex', $route);

		$route = new Route($uri2, NULL);

		$this->assertAttributeSame(array(), '_regex', $route);
	}

	/**
	 * When we pass custom regex to the route's constructor it should it
	 * in leu of the default. This does not apply to callback/lambda routes
	 *
	 * @test
	 * @covers Route::__construct
	 * @covers Route::compile
	 */
	public function test_route_uses_custom_regex_passed_to_constructor()
	{
		$regex = array('id' => '[0-9]{1,2}');

		$route = new Route('<controller>(/<action>(/<id>))', $regex);

		$this->assertAttributeSame($regex, '_regex', $route);
		$this->assertAttributeContains(
			$regex['id'],
			'_route_regex',
			$route
		);
	}

	/**
	 * Provider for test_matches_returns_false_on_failure
	 *
	 * @return array
	 */
	public function provider_matches_returns_false_on_failure()
	{
		return array(
			array('projects/(<project_id>/(<controller>(/<action>(/<id>))))', 'apple/pie'),
			array(array('Route_Holder', 'default_callback'), 'apple/pie'),
		);
	}

	/**
	 * Route::matches() should return false if the route doesn't match against a uri
	 *
	 * @dataProvider provider_matches_returns_false_on_failure
	 *
	 * @test
	 * @covers Route::matches
	 */
	public function test_matches_returns_false_on_failure($uri, $match)
	{
		$route = new Route($uri);

		$this->assertSame(FALSE, $route->matches($match));
	}

	/**
	 * Provider for test_matches_returns_array_of_parameters_on_successful_match
	 *
	 * @return array
	 */
	public function provider_matches_returns_array_of_parameters_on_successful_match()
	{
		return array(
			array(
				'(<controller>(/<action>(/<id>)))',
				'welcome/index',
				'welcome',
				'index',
			),
			array(
				array('Route_Holder', 'matches_returns_array_of_parameters_on_successful_match'),
				'apple/pie',
				'welcome',
				'index',
			),
		);
	}

	/**
	 * Route::matches() should return an array of parameters when a match is made
	 * An parameters that are not matched should not be present in the array of matches
	 *
	 * @dataProvider provider_matches_returns_array_of_parameters_on_successful_match
	 *
	 * @test
	 * @covers Route::matches
	 */
	public function test_matches_returns_array_of_parameters_on_successful_match($uri, $m, $c, $a)
	{
		$route = new Route($uri);

		$matches = $route->matches($m);

		$this->assertInternalType('array', $matches);
		$this->assertArrayHasKey('controller', $matches);
		$this->assertArrayHasKey('action', $matches);
		$this->assertArrayNotHasKey('id', $matches);
		// $this->assertSame(5, count($matches));
		$this->assertSame($c, $matches['controller']);
		$this->assertSame($a, $matches['action']);
	}

	/**
	 * Provider for test_matches_returns_array_of_parameters_on_successful_match
	 *
	 * @return array
	 */
	public function provider_defaults_are_used_if_params_arent_specified()
	{
		return array(
			array(
				'<controller>(/<action>(/<id>))',
				NULL,
				array('controller' => 'welcome', 'action' => 'index'),
				'welcome',
				'index',
				'unit/test/1',
				array(
					'controller' => 'unit',
					'action' => 'test',
					'id' => '1'
				),
				'welcome',
			),
			array(
				'(<controller>(/<action>(/<id>)))',
				NULL,
				array('controller' => 'welcome', 'action' => 'index'),
				'welcome',
				'index',
				'unit/test/1',
				array(
					'controller' => 'unit',
					'action' => 'test',
					'id' => '1'
				),
				'',
			),
			array(
				array('Route_Holder', 'default_return_callback'),
				'(<controller>(/<action>(/<id>)))',
				array('controller' => 'welcome', 'action' => 'index'),
				'welcome',
				'index',
				'unit/test/1',
				array(
					'controller' => 'unit',
					'action' => 'test',
					'id' => '1'
				),
				'',
			),
		);
	}

	/**
	 * Defaults specified with defaults() should be used if their values aren't
	 * present in the uri
	 *
	 * @dataProvider provider_defaults_are_used_if_params_arent_specified
	 *
	 * @test
	 * @covers Route::matches
	 */
	public function test_defaults_are_used_if_params_arent_specified($uri, $regex, $defaults, $c, $a, $test_uri, $test_uri_array, $default_uri)
	{
		$route = new Route($uri, $regex);
		$route->defaults($defaults);

		$this->assertSame($defaults, $route->defaults());

		$matches = $route->matches($default_uri);

		$this->assertInternalType('array', $matches);
		$this->assertArrayHasKey('controller', $matches);
		$this->assertArrayHasKey('action', $matches);
		$this->assertArrayNotHasKey('id', $matches);
		// $this->assertSame(4, count($matches));
		$this->assertSame($c, $matches['controller']);
		$this->assertSame($a, $matches['action']);
		$this->assertSame($test_uri, $route->uri($test_uri_array));
		$this->assertSame($default_uri, $route->uri());
	}

	/**
	 * Provider for test_required_parameters_are_needed
	 *
	 * @return array
	 */
	public function provider_required_parameters_are_needed()
	{
		return array(
			array(
				'admin(/<controller>(/<action>(/<id>)))',
				'admin',
				'admin/users/add',
			),
			array(
				array('Route_Holder', 'required_parameters_are_needed'),
				'admin',
				'admin/users/add',
			),
		);
	}

	/**
	 * This tests that routes with required parameters will not match uris without them present
	 *
	 * @dataProvider provider_required_parameters_are_needed
	 *
	 * @test
	 * @covers Route::matches
	 */
	public function test_required_parameters_are_needed($uri, $matches_route1, $matches_route2)
	{
		$route = new Route($uri);

		$this->assertFalse($route->matches(''));

		$matches = $route->matches($matches_route1);

		$this->assertInternalType('array', $matches);

		$matches = $route->matches($matches_route2);

		$this->assertInternalType('array', $matches);
		// $this->assertSame(5, count($matches));
		$this->assertArrayHasKey('controller', $matches);
		$this->assertArrayHasKey('action', $matches);
	}

	/**
	 * Provider for test_required_parameters_are_needed
	 *
	 * @return array
	 */
	public function provider_reverse_routing_returns_routes_uri_if_route_is_static()
	{
		return array(
			array(
				'info/about_us',
				NULL,
				'info/about_us',
				array('some' => 'random', 'params' => 'to confuse'),
			),
			array(
				array('Route_Holder', 'reverse_routing_returns_routes_uri_if_route_is_static'),
				'info/about_us',
				'info/about_us',
				array('some' => 'random', 'params' => 'to confuse'),
			),
		);
	}

	/**
	 * This tests the reverse routing returns the uri specified in the route
	 * if it's a static route
	 *
	 * A static route is a route without any parameters
	 *
	 * @dataProvider provider_reverse_routing_returns_routes_uri_if_route_is_static
	 *
	 * @test
	 * @covers Route::uri
	 */
	public function test_reverse_routing_returns_routes_uri_if_route_is_static($uri, $regex, $target_uri, $uri_params)
	{
		$route = new Route($uri, $regex);

		$this->assertSame($target_uri, $route->uri($uri_params));
	}

	/**
	 * Provider for test_uri_throws_exception_if_required_params_are_missing
	 *
	 * @return array
	 */
	public function provider_uri_throws_exception_if_required_params_are_missing()
	{
		return array(
			array(
				'<controller>(/<action)',
				NULL,
				array('action' => 'awesome-action'),
			),
			array(
				array('Route_Holder', 'default_return_callback'),
				'<controller>(/<action)',
				array('action' => 'awesome-action'),
			),
		);
	}

	/**
	 * When Route::uri is working on a uri that requires certain parameters to be present
	 * (i.e. <controller> in '<controller(/<action)') then it should throw an exception
	 * if the param was not provided
	 *
	 * @dataProvider provider_uri_throws_exception_if_required_params_are_missing
	 *
	 * @test
	 * @covers Route::uri
	 */
	public function test_uri_throws_exception_if_required_params_are_missing($uri, $regex, $uri_array)
	{
		$route = new Route($uri, $regex);

		try
		{
			$route->uri($uri_array);

			$this->fail('Route::uri should throw exception if required param is not provided');
		}
		catch(Exception $e)
		{
			$this->assertInstanceOf('Kohana_Exception', $e);
			// Check that the error in question is about the controller param
			$this->assertContains('controller', $e->getMessage());
		}
	}

	/**
	 * Provider for test_uri_fills_required_uri_segments_from_params
	 *
	 * @return array
	 */
	public function provider_uri_fills_required_uri_segments_from_params()
	{
		return array(
			array(
				'<controller>/<action>(/<id>)',
				NULL,
				'users/edit',
				array(
					'controller' => 'users',
					'action'     => 'edit',
				),
				'users/edit/god',
				array(
					'controller' => 'users',
					'action'     => 'edit',
					'id'         => 'god',
				),
			),
			array(
				array('Route_Holder', 'default_return_callback'),
				'<controller>/<action>(/<id>)',
				'users/edit',
				array(
					'controller' => 'users',
					'action'     => 'edit',
				),
				'users/edit/god',
				array(
					'controller' => 'users',
					'action'     => 'edit',
					'id'         => 'god',
				),
			),
		);
	}

	/**
	 * The logic for replacing required segments is separate (but similar) to that for
	 * replacing optional segments.
	 *
	 * This test asserts that Route::uri will replace required segments with provided
	 * params
	 *
	 * @dataProvider provider_uri_fills_required_uri_segments_from_params
	 *
	 * @test
	 * @covers Route::uri
	 */
	public function test_uri_fills_required_uri_segments_from_params($uri, $regex, $uri_string1, $uri_array1, $uri_string2, $uri_array2)
	{
		$route = new Route($uri, $regex);

		$this->assertSame(
			$uri_string1,
			$route->uri($uri_array1)
		);

		$this->assertSame(
			$uri_string2,
			$route->uri($uri_array2)
		);
	}

	/**
	 * Provides test data for test_composing_url_from_route()
	 * @return array
	 */
	public function provider_composing_url_from_route()
	{
		return array(
			array('/'),
			array('/news/view/42', array('controller' => 'news', 'action' => 'view', 'id' => 42)),
			array('http://kohanaframework.org/news', array('controller' => 'news'), 'http')
		);
	}

	/**
	 * Tests Route::url()
	 *
	 * Checks the url composing from specific route via Route::url() shortcut
	 *
	 * @test
	 * @dataProvider provider_composing_url_from_route
	 * @param string $expected
	 * @param array $params
	 * @param boolean $protocol
	 */
	public function test_composing_url_from_route($expected, $params = NULL, $protocol = NULL)
	{
		Route::set('foobar', '(<controller>(/<action>(/<id>)))')
			->defaults(array(
				'controller' => 'welcome',
			)
		);

		$this->setEnvironment(array(
			'_SERVER' => array('HTTP_HOST' => 'kohanaframework.org'),
			'Kohana::$base_url' => '/',
			'Kohana::$index_file' => '',
		));

		$this->assertSame($expected, Route::url('foobar', $params, $protocol));
	}

	/**
	 * Tests Route::compile()
	 *
	 * Makes sure that compile will use custom regex if specified
	 *
	 * @test
	 * @covers Route::compile
	 */
	public function test_compile_uses_custom_regex_if_specificed()
	{
		$compiled = Route::compile(
			'<controller>(/<action>(/<id>))',
			array(
				'controller' => '[a-z]+',
				'id' => '\d+',
			)
		);

		$this->assertSame('#^(?P<controller>[a-z]+)(?:/(?P<action>[^/.,;?\n]++)(?:/(?P<id>\d+))?)?$#uD', $compiled);
	}

	/**
	 * Tests Route::is_external(), ensuring the host can return
	 * whether internal or external host
	 */
	public function test_is_external_route_from_host()
	{
		// Setup local route
		Route::set('internal', 'local/test/route')
			->defaults(array(
				'controller' => 'foo',
				'action'     => 'bar'
				)
			);

		// Setup external route
		Route::set('external', 'local/test/route')
			->defaults(array(
				'controller' => 'foo',
				'action'     => 'bar',
				'host'       => 'http://kohanaframework.org'
				)
			);

		// Test internal route
		$this->assertFalse(Route::get('internal')->is_external());

		// Test external route
		$this->assertTrue(Route::get('external')->is_external());
	}

	/**
	 * Provider for test_external_route_includes_params_in_uri
	 *
	 * @return array
	 */
	public function provider_external_route_includes_params_in_uri()
	{
		return array(
			array(
				'<controller>/<action>',
				array(
					'controller'  => 'foo',
					'action'      => 'bar',
					'host'        => 'kohanaframework.org'
				),
				'http://kohanaframework.org/foo/bar'
			),
			array(
				'<controller>/<action>',
				array(
					'controller'  => 'foo',
					'action'      => 'bar',
					'host'        => 'http://kohanaframework.org'
				),
				'http://kohanaframework.org/foo/bar'
			),
			array(
				'foo/bar',
				array(
					'controller'  => 'foo',
					'host'        => 'http://kohanaframework.org'
				),
				'http://kohanaframework.org/foo/bar'
			),
		);
	}

	/**
	 * Tests the external route include route parameters
	 *
	 * @dataProvider provider_external_route_includes_params_in_uri
	 */
	public function test_external_route_includes_params_in_uri($route, $defaults, $expected_uri)
	{
		Route::set('test', $route)
			->defaults($defaults);

		$this->assertSame($expected_uri, Route::get('test')->uri());
	}
}
