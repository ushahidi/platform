<?php

/**
 * Tests for Twitter class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\DataSource;

use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\Core\Entity\Config;
use Ushahidi\DataSource\Twitter\Twitter;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class TwitterDataSourceTest extends TestCase
{
    public function testSendWithoutConfig()
    {
        $mockTwitterOAuth = M::mock(\Abraham\TwitterOAuth\TwitterOAuth::class);
        $mockResponse = M::mock(\Abraham\TwitterOAuth\Response::class);
        $mockRepo = M::mock(\Ushahidi\Modules\V3\Repository\ConfigRepository::class);

        $twitter = new Twitter(
            [],
            $mockRepo,
            function ($a, $b, $c, $d) use ($mockTwitterOAuth) {
                return $mockTwitterOAuth;
            }
        );

        $response = $twitter->send('ushahidi', 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('failed', $response[0]);
        $this->assertEquals(false, $response[1]);
    }

    public function testSend()
    {
        $mockTwitterOAuth = M::mock(\Abraham\TwitterOAuth\TwitterOAuth::class);
        $mockTwitterOAuth->shouldReceive('setTimeouts')->once();
        $mockResponse = M::mock(\Abraham\TwitterOAuth\Response::class);
        $mockRepo = M::mock(\Ushahidi\Modules\V3\Repository\ConfigRepository::class);

        $twitter = new Twitter(
            [
                'consumer_key' => '',
                'consumer_secret' => '',
                'oauth_access_token' => '',
                'oauth_access_token_secret' =>  '',
            ],
            $mockRepo,
            function ($a, $b, $c, $d) use ($mockTwitterOAuth) {
                return $mockTwitterOAuth;
            }
        );

        $mockTwitterOAuth
            ->shouldReceive('post')->once()
            ->with(
                'statuses/update',
                [
                    'status' => '@ushahidi A message',
                ]
            )
            ->andReturn($mockResponse);
        $mockResponse->id = 1234564;

        $response = $twitter->send('ushahidi', 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('sent', $response[0]);
        $this->assertEquals('1234564', $response[1]);
    }

    public function testSendFailed()
    {
        $mockTwitterOAuth = M::mock(\Abraham\TwitterOAuth\TwitterOAuth::class);
        $mockTwitterOAuth->shouldReceive('setTimeouts')->once();
        $mockResponse = M::mock(\Abraham\TwitterOAuth\Response::class);
        $mockRepo = M::mock(\Ushahidi\Modules\V3\Repository\ConfigRepository::class);

        $twitter = new Twitter(
            [
                'consumer_key' => '',
                'consumer_secret' => '',
                'oauth_access_token' => '',
                'oauth_access_token_secret' =>  '',
            ],
            $mockRepo,
            function ($a, $b, $c, $d) use ($mockTwitterOAuth) {
                return $mockTwitterOAuth;
            }
        );

        $mockTwitterOAuth
            ->shouldReceive('post')->once()
            ->with(
                'statuses/update',
                [
                    'status' => '@ushahidi A message',
                ]
            )
            ->andReturn($mockResponse);
        $mockTwitterOAuth->shouldReceive('setTimeouts')->once();

        $response = $twitter->send('ushahidi', 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('failed', $response[0]);
        $this->assertEquals(false, $response[1]);

        $mockTwitterOAuth
            ->shouldReceive('post')
            ->with(
                'statuses/update',
                [
                    'status' => '@ushahidi A message',
                ]
            )
            ->once()
            ->andThrow(M::mock(\Abraham\TwitterOAuth\TwitterOAuthException::class));

        $response = $twitter->send('ushahidi', 'A message');

        $this->assertIsArray($response);
        $this->assertEquals('failed', $response[0]);
        $this->assertEquals(false, $response[1]);
    }

    public function testFetch()
    {
        $mockTwitterOAuth = M::mock(\Abraham\TwitterOAuth\TwitterOAuth::class);
        $mockTwitterOAuth->shouldReceive('setTimeouts')->once();
        $mockResponse = M::mock(\Abraham\TwitterOAuth\Response::class);
        $mockRepo = M::mock(\Ushahidi\Modules\V3\Repository\ConfigRepository::class);

        $twitter = new Twitter(
            [
                'consumer_key' => '',
                'consumer_secret' => '',
                'oauth_access_token' => '',
                'oauth_access_token_secret' =>  '',
                'twitter_search_terms' => '#ushahidi,#test',
            ],
            $mockRepo,
            function ($a, $b, $c, $d) use ($mockTwitterOAuth) {
                return $mockTwitterOAuth;
            }
        );

        $config = new Config([
            'id' => 'twitter',
            'since_id' => 1234,
            'search_terms' => '#ushahidi,#test',
        ]);

        $mockRepo->shouldReceive('get')->atLeast()->once()
            ->with('twitter')
            ->andReturn($config);
        $mockRepo->shouldReceive('update')->once()
            ->with($config);

        $mockTwitterOAuth->shouldReceive('setDecodeJsonAsArray')->once();

        $mockTwitterOAuth->shouldReceive('get')->once()
            ->with('search/tweets', [
                'q' => '#ushahidi OR #test',
                'since_id' => 1234,
                'count' => 50,
                'result_type' => 'recent',
            ])
            ->andReturn([
                'statuses' => [
                    [
                        'id' => 'abc123',
                        'user' => [
                            'screen_name' => 'ushahidi',
                            'id_str' => '1112222223',
                        ],
                        'text' => 'Test message',
                        'coordinates' => [
                            'coordinates' => [
                                -75.14310264,
                                40.05701649,
                            ],
                            'type' => 'Point',
                        ],
                        'created_at' => 'Thu Apr 06 15:24:15 +0000 2017',
                    ],
                    [
                        'id' => 'abc124',
                        'user' => [
                            'screen_name' => 'ushahidi',
                            'id_str' => '1112222222',
                        ],
                        'text' => 'Test message 2',
                        'retweeted_status' => [
                            'text' => 'notsurewhatthisnormallyis',
                        ],
                        'created_at' => 'Thu Apr 06 15:24:15 +0000 2017',
                    ],
                    [
                        'id' => 'abc125',
                        'user' => [
                            'screen_name' => 'someone',
                            'id_str' => '1112222225',
                        ],
                        'text' => 'Test message 3',
                        'created_at' => 'Thu Apr 06 15:24:15 +0000 2017',
                    ],
                    [
                        'id' => 'abc126',
                        'user' => [
                            'id_str' => '12344494949',
                            'screen_name' => 'someone',
                        ],
                        'text' => 'Test message 4',
                        'place' => [
                            'attributes' => [],
                            'bounding_box' => [
                                'coordinates' => [
                                    [
                                        [
                                            -77.119759000000002,
                                            38.791645000000003,
                                        ],
                                        [
                                            -76.909392999999994,
                                            38.791645000000003,
                                        ],
                                        [
                                            -76.909392999999994,
                                            38.995547999999999,
                                        ],
                                        [
                                            -77.119759000000002,
                                            38.995547999999999,
                                        ],
                                    ],
                                ],
                                'type' => 'Polygon',
                            ],
                            'country' => 'United States',
                            'country_code' => 'US',
                            'full_name' => 'Washington, DC',
                            'id' => '01fbe706f872cb32',
                            'name' => 'Washington',
                            'place_type' => 'city',
                            'url' => 'http://api.twitter.com/1/geo/id/01fbe706f872cb32.json',
                        ],
                        'created_at' => 'Thu Apr 06 15:24:15 +0000 2017',
                    ],
                ],
            ]);

        $messages = $twitter->fetch();

        $this->assertEquals([
            [
                'type' => 'twitter',
                'contact_type' => 'twitter',
                'from' => '1112222223', // twitter user id
                'message' => 'https://twitter.com/1112222223/status/abc123',
                'to' => null,
                'title' => 'From twitter on Thu Apr 06 15:24:15 +0000 2017',
                'data_source_message_id' => 'abc123',
                'additional_data' => [],
                'datetime' =>  'Thu Apr 06 15:24:15 +0000 2017',
            ],
            [
                'type' => 'twitter',
                'contact_type' => 'twitter',
                'from' => '1112222225', // twitter user id
                'message' => 'https://twitter.com/1112222225/status/abc125',
                'to' => null,
                'title' => 'From twitter on Thu Apr 06 15:24:15 +0000 2017',
                'data_source_message_id' => 'abc125',
                'additional_data' => [],
                'datetime' =>  'Thu Apr 06 15:24:15 +0000 2017',
            ],
            [
                'type' => 'twitter',
                'contact_type' => 'twitter',
                'from' => '12344494949', //twitter user id
                'message' => 'https://twitter.com/12344494949/status/abc126',
                'to' => null,
                'title' => 'From twitter on Thu Apr 06 15:24:15 +0000 2017',
                'data_source_message_id' => 'abc126',
                'additional_data' => [],
                'datetime' =>  'Thu Apr 06 15:24:15 +0000 2017',
            ],
        ], $messages);
    }
}
