<?php

/**
 * Tests for Twilio class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\DataSource;

use Tests\TestCase;
use Mockery as M;

use Ushahidi\App\DataSource\Twilio\Twilio;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class TwilioDataSourceTest extends TestCase
{
    public function testSendWithoutConfig()
    {
        // Unconfigured send should fail gracefully
        $twilio = new Twilio(
            [],
            function () {
                return M::mock(\Twilio\Rest\Client::class);
            }
        );
        $response = $twilio->send(1234, "A message");

        $this->assertInternalType('array', $response);
        $this->assertEquals('failed', $response[0]);
        $this->assertFalse($response[1]);
    }

    public function testSend()
    {
        $mockTwilio = M::mock(\Twilio\Rest\Client::class);
        $mockMessages = M::mock(\Twilio\Rest\Api\V2010\Account\MessageList::class);
        $mockMessage = M::mock(\Twilio\Rest\Api\V2010\Account\MessageInstance::class);

        $twilio = new Twilio([
            'account_sid' => 'secret',
            'auth_token' => ''
        ], function ($accountSid, $authToken) use ($mockTwilio) {
            return $mockTwilio;
        });

        $mockTwilio->messages = $mockMessages;
        $mockMessages->shouldReceive('create')->once()->andReturn($mockMessage);
        $mockMessage->sid = 'test';

        $response = $twilio->send(1234, "A message");

        $this->assertInternalType('array', $response);
        $this->assertEquals('sent', $response[0]);
        $this->assertEquals('test', $response[1]);
    }

    public function testSendFails()
    {
        $mockTwilio = M::mock(\Twilio\Rest\Client::class);
        $mockMessages = M::mock(\Twilio\Rest\Api\V2010\Account\MessageList::class);

        $twilio = new Twilio([
            'account_sid' => 'secret',
            'auth_token' => ''
        ], function ($accountSid, $authToken) use ($mockTwilio) {
            return $mockTwilio;
        });

        $mockTwilio->messages = $mockMessages;
        $mockMessages->shouldReceive('create')->once()->andThrow(M::mock(\Twilio\Exceptions\RestException::class));

        $response = $twilio->send(1234, "A message");

        $this->assertInternalType('array', $response);
        $this->assertEquals('failed', $response[0]);
        $this->assertEquals(false, $response[1]);
    }

    public function testVerifySid()
    {
        $twilio = new Twilio([
            'sms_auto_response' => "an auto response",
            'account_sid' => 'secret'
        ]);

        $this->assertTrue($twilio->verifySid('secret'));
        $this->assertFalse($twilio->verifySid('notsecret'));

        $twilio = new Twilio([
            'sms_auto_response' => "an auto response"
        ]);

        $this->assertFalse($twilio->verifySid('secret'));
    }

    public function testGetSmsAutoResponse()
    {
        $twilio = new Twilio([
            'sms_auto_response' => "an auto response",
        ]);

        $this->assertEquals("an auto response", $twilio->getSmsAutoResponse());

        $twilio = new Twilio([]);
        $this->assertFalse($twilio->getSmsAutoResponse());
    }
}
