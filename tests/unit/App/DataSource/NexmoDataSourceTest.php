<?php

/**
 * Tests for Nexmo class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\DataSource;

use Tests\TestCase;
use Mockery as M;

use Ushahidi\App\DataSource\Nexmo\Nexmo;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class NexmoDataSourceTest extends TestCase
{
    public function testSendWithoutConfig()
    {
        // Unconfigured send should fail gracefully
        $nexmo = new Nexmo(
            [],
            function () {
                return M::mock(\Nexmo\Client::class);
            }
        );
        $response = $nexmo->send(1234, "A message");

        $this->assertInternalType('array', $response);
        $this->assertEquals('failed', $response[0]);
        $this->assertFalse($response[1]);
    }

    public function testSend()
    {
        $mockNexmo = M::mock(\Nexmo\Client::class);
        $mockMessage = M::mock(\Nexmo\Message\Message::class);

        $nexmo = new Nexmo([
            'api_key' => 'secret',
            'api_secret' => '1234'
        ], function ($accountSid, $authToken) use ($mockNexmo) {
            return $mockNexmo;
        });

        $mockNexmo->shouldReceive('message->send')->once()->andReturn($mockMessage);
        $mockMessage->shouldReceive('getMessageId')->once()->andReturn(1234);

        $response = $nexmo->send(1234, "A message");

        $this->assertInternalType('array', $response);
        $this->assertEquals('sent', $response[0]);
        $this->assertEquals(1234, $response[1]);
    }

    public function testSendFails()
    {
        $mockNexmo = M::mock(\Nexmo\Client::class);
        $mockMessage = M::mock(\Nexmo\Message\Message::class);

        $nexmo = new Nexmo([
            'api_key' => 'secret',
            'api_secret' => '1234'
        ], function ($accountSid, $authToken) use ($mockNexmo) {
            return $mockNexmo;
        });

        $mockNexmo->shouldReceive('message->send')->once()->andThrow(M::mock(\Nexmo\Client\Exception\Exception::class));

        $response = $nexmo->send(1234, "A message");

        $this->assertInternalType('array', $response);
        $this->assertEquals('failed', $response[0]);
        $this->assertEquals(false, $response[1]);
    }
}
