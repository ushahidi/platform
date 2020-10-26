<?php

/**
 * Unit tests for Signature Auth Middleware
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\Http\Middleware;

use Ushahidi\App\Multisite\MultisiteManager;
use Ushahidi\App\Multisite\Site;
use Ushahidi\App\Http\Middleware\CheckDemoExpiration;
use Illuminate\Http\Request;
use Tests\TestCase;
use Mockery as M;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class CheckDemoExpirationTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testMultisiteDisabled()
    {
        $multisite = M::mock(MultisiteManager::class);
        $request = M::mock(Request::class);

        $multisite->shouldReceive('enabled')->andReturn(false);

        $middleware = new CheckDemoExpiration($multisite);
        $response = $middleware->handle($request, function () {
            return 'called';
        });

        $this->assertEquals($response, 'called');
    }

    public function testGetRequest()
    {
        $multisite = M::mock(MultisiteManager::class);
        $request = M::mock(Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('isMethod')->with('get')->andReturn(true);

        $middleware = new CheckDemoExpiration($multisite);
        $response = $middleware->handle($request, function () {
            return 'called';
        });

        $this->assertEquals($response, 'called');
    }

    public function testPaidTierRequest()
    {
        $multisite = M::mock(MultisiteManager::class);
        $request = M::mock(Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('isMethod')->with('get')->andReturn(false);
        $multisite->shouldReceive('getSite')->andReturn(new Site([
            'tier' => 'paid'
        ]));

        $middleware = new CheckDemoExpiration($multisite);
        $response = $middleware->handle($request, function () {
            return 'called';
        });

        $this->assertEquals($response, 'called');
    }

    public function testDemoTierNotExpired()
    {
        $multisite = M::mock(MultisiteManager::class);
        $request = M::mock(Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('isMethod')->with('get')->andReturn(false);
        $multisite->shouldReceive('getSite')->andReturn(new Site([
            'tier' => 'demo',
            'expiration_date' => date('Y-m-d H:i:s', strtotime('tomorrow')),
            'extension_date' => null,
        ]));

        $middleware = new CheckDemoExpiration($multisite);
        $response = $middleware->handle($request, function () {
            return 'called';
        });

        $this->assertEquals($response, 'called');
    }

    public function testDemoTierExpired()
    {
        $multisite = M::mock(MultisiteManager::class);
        $request = M::mock(Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('isMethod')->with('get')->andReturn(false);
        $multisite->shouldReceive('getSite')->andReturn(new Site([
            'tier' => 'demo',
            'expiration_date' => date('Y-m-d H:i:s', strtotime('yesterday')),
            'extension_date' => null,
        ]));

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('The demo period for this deployment has expired.');

        $middleware = new CheckDemoExpiration($multisite);
        $response = $middleware->handle($request, function () {
            return 'called';
        });
    }

    public function testDemoTierExtended()
    {
        $multisite = M::mock(MultisiteManager::class);
        $request = M::mock(Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('isMethod')->with('get')->andReturn(false);
        $multisite->shouldReceive('getSite')->andReturn(new Site([
            'tier' => 'demo',
            'expiration_date' => date('Y-m-d H:i:s', strtotime('yesterday')),
            'extension_date' => date('Y-m-d H:i:s', strtotime('tomorrow')),
        ]));

        $middleware = new CheckDemoExpiration($multisite);
        $response = $middleware->handle($request, function () {
            return 'called';
        });
        $this->assertEquals($response, 'called');
    }

    public function testDemoTierExtensionExpired()
    {
        $multisite = M::mock(MultisiteManager::class);
        $request = M::mock(Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('isMethod')->with('get')->andReturn(false);
        $multisite->shouldReceive('getSite')->andReturn(new Site([
            'tier' => 'demo',
            'expiration_date' => date('Y-m-d H:i:s', strtotime('yesterday')),
            'extension_date' => date('Y-m-d H:i:s', strtotime('yesterday')),
        ]));

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('The demo period for this deployment has expired.');

        $middleware = new CheckDemoExpiration($multisite);
        $response = $middleware->handle($request, function () {
            return 'called';
        });
    }
}
