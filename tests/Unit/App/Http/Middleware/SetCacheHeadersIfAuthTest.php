<?php

/**
 * Unit tests for Signature Auth Middleware
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2020 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\App\Http\Middleware;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Http\Request;
use Mockery as M;
use Ushahidi\Tests\TestCase;
use App\Http\Middleware\SetCacheHeadersIfAuth;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class SetCacheHeadersIfAuthTest extends TestCase
{
    protected function mockAuth(bool $isAuthenticated) : AuthFactory
    {
        $auth = M::mock(AuthFactory::class);

        $guard = M::mock(AuthGuard::class);
        $guard->shouldReceive('guest')->andReturn(! $isAuthenticated);
        $auth->shouldReceive('guard')->andReturn($guard);

        return $auth;
    }

    /**
     * Tests the middleware when :
     *   - caching is enabled in the app
     *   - no check is made on the auth status of the request
     *   - default caching options preset selected
     *
     * As a result, the response indicates public cache control for a certain period of time
     */
    public function testGuestMinimalCache()
    {
        // Enable minimal caching
        config(['routes.cache_control.level' => 'minimal']);

        $middleware = new SetCacheHeadersIfAuth(M::mock(AuthFactory::class));

        $request = new Request();
        $return = $middleware->handle(
            $request,
            function ($r) use ($request) {
                return response()->json(['done' => 'yes']);
            },
            'minimal',              /* threshold level for caching the route */
            null,                   /* no auth guard check */
            'preset/default'
        );

        // Ensure we have max-age or public clauses in cache-control header
        $cache_control = $return->headers->get('cache-control') ?? '';
        $this->assertMatchesRegularExpression('/max-age/i', $cache_control);
        $this->assertMatchesRegularExpression('/public/i', $cache_control);

        // Ensure that authentication variance is added
        $this->assertMatchesRegularExpression(
            '/Authorization/i',
            $return->headers->get('vary')
        );
    }

    /**
     * Test the middleware when :
     *   - caching is disabled in the app
     *   - no check is made on the auth status of the request
     *   - default caching options preset selected
     *
     * As a result, the response doesn't indicate that it should be cached
     */
    public function testGuestCacheDisabled()
    {
        // Disable caching
        config(['routes.cache_control.level' => 'off']);

        $middleware = new SetCacheHeadersIfAuth(M::mock(AuthFactory::class));

        $request = new Request();
        $return = $middleware->handle(
            $request,
            function ($r) use ($request) {
                return response()->json(['done' => 'yes']);
            },
            'minimal',              /* threshold level for caching the route */
            null,                   /* auth guard */
            'preset/default'
        );

        // Ensure we don't have max-age or public clauses in cache-control header
        $cache_control = $return->headers->get('cache-control') ?? '';
        $this->assertDoesNotMatchRegularExpression('/max-age/i', $cache_control);
        $this->assertDoesNotMatchRegularExpression('/public/i', $cache_control);
    }

    /**
     * Test the middleware when :
     *   - caching is enabled in the app
     *   - a check is made on the auth status of the request
     *   - default caching options preset selected
     *
     * As a result, the response doesn't indicate that it should be cached
     */
    public function testAuthDoesntCache()
    {
        // Enable minimal caching
        config(['routes.cache_control.level' => 'minimal']);

        // Mock an authenticated user
        $middleware = new SetCacheHeadersIfAuth($this->mockAuth(true));

        $request = new Request();
        $return = $middleware->handle(
            $request,
            function ($r) use ($request) {
                return response()->json(['done' => 'yes']);
            },
            'minimal',              /* threshold level for caching the route */
            'api',                  /* auth guard */
            'preset/dont-cache'
        );

        // Ensure we got a no-store cache-control header
        $this->assertMatchesRegularExpression(
            '/no-store/i',
            $return->headers->get('cache-control')
        );
        // Ensure that authentication variance is added
        $this->assertMatchesRegularExpression(
            '/Authorization/i',
            $return->headers->get('vary')
        );
    }

    /**
     * Test the middleware when :
     *   - caching is enabled in the app
     *   - a check is made on the auth status of the request
     *   - the user making the request is a guest
     *
     * As a result:
     *   - there should be no changes in the cache-control header
     *   - the response should indicate variation on Authorization headers
     */
    public function testAuthVary()
    {
        // Enable minimal caching
        config(['routes.cache_control.level' => 'minimal']);

        // Mock a guest user
        $middleware = new SetCacheHeadersIfAuth($this->mockAuth(false));

        $request = new Request();
        $pre_cache_control = null;
        $return = $middleware->handle(
            $request,
            function ($r) use ($request, &$pre_cache_control) {
                $resp = response()->json(['done' => 'yes']);
                $pre_cache_control = $resp->headers->get('cache-control');

                return $resp;
            },
            'minimal',              /* threshold level for caching the route */
            'api',                  /* auth guard */
            'preset/dont-cache'
        );

        // Ensure that cache-control header is not modified
        $this->assertEquals(
            $pre_cache_control,
            $return->headers->get('cache-control') ?? ''
        );
        // Ensure that authentication variance is added
        $this->assertMatchesRegularExpression(
            '/Authorization/i',
            $return->headers->get('vary')
        );
    }
}
