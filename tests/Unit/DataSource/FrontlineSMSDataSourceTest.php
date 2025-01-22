<?php

/**
 * Tests for Frontline class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\DataSource;

use GuzzleHttp\Client as GuzzleClient;
use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\DataSource\FrontlineSMS\FrontlineSMS;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class FrontlineSMSDataSourceTest extends TestCase
{
    public function testSendWithoutConfig()
    {
        // Unconfigured send should fail gracefully
        $sms = new FrontlineSMS(
            [],
            M::mock(GuzzleClient::class)
        );
        $response = $sms->send(1234, 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('failed', $response[0]);
        $this->assertFalse($response[1]);
    }

    public function testSend()
    {
        $mockGuzzle = M::mock(GuzzleClient::class);
        $mockResponse = M::mock(\Psr\Http\Message\ResponseInterface::class);

        $sms = new FrontlineSMS([
            'key' => 'secret',
        ], $mockGuzzle);

        $mockGuzzle->shouldReceive('request')->once()->with(
            'POST',
            'https://cloud.frontlinesms.com/api/1/webhook',
            [
                'headers' => [
                    'Accept'               => 'application/json',
                    'Content-Type'         => 'application/json',
                ],
                'json' => [
                    'apiKey' => 'secret',
                    'payload' => [
                        'message' => 'A message',
                        'recipients' => [
                            [
                                'type' => 'mobile',
                                'value' => 1234,
                            ],
                        ],
                    ],
                ],
            ]
        )->andReturn($mockResponse);

        $mockResponse->shouldReceive('getStatusCode')->once()->andReturn(200);

        $response = $sms->send(1234, 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('sent', $response[0]);
        $this->assertEquals(null, $response[1]);
    }

    public function testSendFails()
    {
        $mockGuzzle = M::mock(GuzzleClient::class);
        $mockResponse = M::mock(\Psr\Http\Message\ResponseInterface::class);
        $mockError = M::mock(\GuzzleHttp\Exception\ClientException::class);

        $sms = new FrontlineSMS([
            'key' => 'secret',
        ], $mockGuzzle);

        $mockGuzzle->shouldReceive('request')->once()->andThrow($mockError);

        $response = $sms->send(1234, 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('failed', $response[0]);
        $this->assertEquals(false, $response[1]);
    }

    public function testVerifySecret()
    {
        $sms = new FrontlineSMS([
            'secret' => 'a secret',
        ]);

        $this->assertTrue($sms->verifySecret('a secret'));
        $this->assertFalse($sms->verifySecret('notsecret'));

        $twilio = new FrontlineSMS([]);

        $this->assertFalse($sms->verifySecret('secret'));
    }
}
