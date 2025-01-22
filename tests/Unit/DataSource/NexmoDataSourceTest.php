<?php

/**
 * Tests for Nexmo class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\DataSource;

use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\DataSource\Nexmo\Nexmo;

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
                return M::mock(\Vonage\Client::class);
            }
        );
        $response = $nexmo->send(1234, 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('failed', $response[0]);
        $this->assertFalse($response[1]);
    }

    public function testSend()
    {
        $mockNexmo = M::mock(\Vonage\Client::class);
        $mockMessage = M::mock(\Vonage\SMS\Collection::class);

        $nexmo = new Nexmo([
            'api_key' => 'secret',
            'api_secret' => '1234',
        ], function ($accountSid, $authToken) use ($mockNexmo) {
            return $mockNexmo;
        });

        $mockNexmo->shouldReceive('sms->send')->once()->andReturn($mockMessage);
        $mockMessage->shouldReceive('current->getMessageId')->once()->andReturn(1234);

        $response = $nexmo->send(1234, 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('sent', $response[0]);
        $this->assertEquals(1234, $response[1]);
    }

    public function testSendFails()
    {
        $mockNexmo = M::mock(\Vonage\Client::class);

        $nexmo = new Nexmo([
            'api_key' => 'secret',
            'api_secret' => '1234',
        ], function ($accountSid, $authToken) use ($mockNexmo) {
            return $mockNexmo;
        });

        $mockNexmo->shouldReceive('sms->send')->once()->andThrow(M::mock(\Vonage\Client\Exception\Exception::class));

        $response = $nexmo->send(1234, 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('failed', $response[0]);
        $this->assertEquals(false, $response[1]);
    }
}
