<?php

/**
 * Tests for Twitter class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Console;

use Tests\TestCase;
use Mockery as M;

use Ushahidi\App\DataSource\Twitter\Twitter;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class TwitterDataSourceTest extends TestCase
{

	public function setUp()
	{
		parent::setUp();
	}

	public function testSendWithoutConfig()
	{
		$mockTwitterOAuth = M::mock(\Abraham\TwitterOAuth\TwitterOAuth::class);
		$mockResponse = M::mock(\Abraham\TwitterOAuth\Response::class);

		$twitter = new Twitter([
		], function ($a, $b, $c, $d) use ($mockTwitterOAuth) {
			return $mockTwitterOAuth;
		});

		$response = $twitter->send('ushahidi', "A message");

		$this->assertInternalType('array', $response);
		$this->assertEquals('failed', $response[0]);
		$this->assertEquals(false, $response[1]);
	}

	public function testSend()
	{
		$mockTwitterOAuth = M::mock(\Abraham\TwitterOAuth\TwitterOAuth::class);
		$mockTwitterOAuth->shouldReceive('setTimeouts');
		$mockResponse = M::mock(\Abraham\TwitterOAuth\Response::class);

		$twitter = new Twitter([
			'consumer_key' => '',
			'consumer_secret' => '',
			'oauth_access_token' => '',
			'oauth_access_token_secret' =>  '',
		], function ($a, $b, $c, $d) use ($mockTwitterOAuth) {
			return $mockTwitterOAuth;
		});

		$mockTwitterOAuth
			->shouldReceive('post')
			->with(
				"statuses/update",
				[
					"status" => '@ushahidi A message'
                ]
            )
			->andReturn($mockResponse);
		$mockResponse->id = 1234564;

		$response = $twitter->send('ushahidi', "A message");

		$this->assertInternalType('array', $response);
		$this->assertEquals('sent', $response[0]);
		$this->assertEquals('1234564', $response[1]);
	}

	public function testSendFailed()
	{
		$mockTwitterOAuth = M::mock(\Abraham\TwitterOAuth\TwitterOAuth::class);
		$mockTwitterOAuth->shouldReceive('setTimeouts');
		$mockResponse = M::mock(\Abraham\TwitterOAuth\Response::class);

		$twitter = new Twitter([
			'consumer_key' => '',
			'consumer_secret' => '',
			'oauth_access_token' => '',
			'oauth_access_token_secret' =>  '',
		], function ($a, $b, $c, $d) use ($mockTwitterOAuth) {
			return $mockTwitterOAuth;
		});

		$mockTwitterOAuth
			->shouldReceive('post')
			->with(
				"statuses/update",
				[
					"status" => '@ushahidi A message'
                ]
            )
			->andReturn($mockResponse);
		$mockTwitterOAuth->shouldReceive('setTimeouts');
		$mockResponse->id = 0;

		$response = $twitter->send('ushahidi', "A message");

		$this->assertInternalType('array', $response);
		$this->assertEquals('failed', $response[0]);
		$this->assertEquals(false, $response[1]);

		$mockTwitterOAuth
			->shouldReceive('post')
			->with(
				"statuses/update",
				[
					"status" => '@ushahidi A message'
                ]
            )
			->andThrow(M::mock(\Abraham\TwitterOAuth\TwitterOAuthException::class));

		$response = $twitter->send('ushahidi', "A message");

		$this->assertInternalType('array', $response);
		$this->assertEquals('failed', $response[0]);
		$this->assertEquals(false, $response[1]);
	}
}
