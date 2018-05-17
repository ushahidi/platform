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

use Ushahidi\App\Http\Middleware\SignatureAuth;
use Ushahidi\Core\Tool\Verifier;
use Illuminate\Http\Request;
use Tests\TestCase;
use Mockery as M;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class SignatureAuthTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        // Set the shared secret
        $this->originalSecret = getenv('PLATFORM_SHARED_SECRET');
        putenv('PLATFORM_SHARED_SECRET=asharedsecret');
    }

    public function tearDown()
    {
        putenv('PLATFORM_SHARED_SECRET=' . $this->originalSecret);

        parent::tearDown();
    }

    public function testValidSignature()
    {
        putenv('PLATFORM_SHARED_SECRET=asharedsecret');
        $verifier = M::mock(Verifier::class);
        $middleware = new SignatureAuth($verifier);
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [],
            ""
        );

        $verifier->shouldReceive('verified')->with(null, null, 'asharedsecret', 'http://:', "")->andReturn(true);

        $return = $middleware->handle($request, function ($r) use ($request) {
            $this->assertSame($request, $r);

            return 'a response';
        });

        $this->assertSame('a response', $return);
    }

    public function testInvalidSignature()
    {
        putenv('PLATFORM_SHARED_SECRET=asharedsecret');
        $verifier = M::mock(Verifier::class);
        $middleware = new SignatureAuth($verifier);
        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [],
            ""
        );

        $verifier->shouldReceive('verified')->with(null, null, 'asharedsecret', 'http://:', "")->andReturn(false);

        try {
            $middleware->handle($request, function ($request) {
                $this->fail('Should have thrown an exception');
            });
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Symfony\Component\HttpKernel\Exception\HttpException::class, $e);
            $this->assertEquals('Forbidden.', $e->getMessage());
            $this->assertEquals(403, $e->getStatusCode());
        }
    }
}
