<?php

/**
 * Tests for DataSourceManager class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\DataSource;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mailer\Message;
use Mockery as M;
use phpmock\mockery\PHPMockery;
use Ushahidi\Tests\TestCase;
use Ushahidi\Contracts\Repository\Entity\MessageRepository;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\Core\Entity\Config;
use Ushahidi\DataSource\Email\Email;
use Ushahidi\Multisite\Site;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class EmailDataSourceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (! defined('FT_UID')) {
            define('FT_UID', 0);
        }
    }

    public function testSend()
    {
        // Mock site
        $site = M::mock(Site::class);
        $site->shouldReceive('getEmail')->andReturn('test@ushahidi.app');
        $site->shouldReceive('getName')->andReturn('The Site');
        $site->shouldReceive('getClientUri')->andReturn('ushahidi.app');
        $site->shouldReceive('getDbConfig')->andReturn([
            'host' => config('database.connections.mysql.host'),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ]);
        $site->shouldReceive('getId')->andReturn(1);
        $this->app->make('multisite')->setSite($site);

        $mockMailer = M::mock(\Illuminate\Contracts\Mail\Mailer::class);
        $mockConfigRepo = M::mock(ConfigRepository::class);

        $email = new Email(
            [],
            $mockMailer,
            null,
            $mockConfigRepo
        );

        $mockMailer->shouldReceive('send')->once()->with(
            'emails/outgoing-message',
            [
                'message_text' => 'A message',
                'site_url' => 'ushahidi.app',
            ],
            M::on(function (\Closure $closure) {
                $mock = M::mock(Message::class);
                $mock->shouldReceive('to')->once()->once()->with('test@ushahidi.com')
                     ->andReturn($mock); // simulate the chaining
                $mock->shouldReceive('from')->once()->once()->with('test@ushahidi.app', 'The Site')
                     ->andReturn($mock); // simulate the chaining
                $mock->shouldReceive('subject')->once()->once()->with('A title')
                     ->andReturn($mock); // simulate the chaining

                $closure($mock);

                return true;
            })
        );

        $response = $email->send('test@ushahidi.com', 'A message', 'A title');

        $this->assertIsArray($response);
        $this->assertEquals('sent', $response[0]);
        $this->assertEquals(false, $response[1]);
    }

    public function testFetch()
    {
        $mockMailer = M::mock(Mailer::class);
        $mockMessageRepo = M::mock(MessageRepository::class);

        $mockMessageRepo->shouldReceive('getLastUID')->once()->andReturn(712);

        $mockConfigRepo = M::mock(ConfigRepository::class);
        $mockConfigRepo->shouldReceive('get')->once()->andReturn(new Config([
                'incoming_type' => 'imap',
                'incoming_server' => 'imap.somewhere.com',
                'incoming_port' => '993',
                'incoming_security' => 'ssl',
                'incoming_username' => 'someuser',
                'incoming_password' => 'mypassword',
                'incoming_all_unread' => 'All',
                'incoming_last_uid' => '712'
        ]));
        $mockConfigRepo->shouldReceive('update')->once()->andReturnUndefined();

        $email = new Email(
            [
                'incoming_type' => 'imap',
                'incoming_server' => 'imap.somewhere.com',
                'incoming_port' => '993',
                'incoming_security' => 'ssl',
                'incoming_username' => 'someuser',
                'incoming_password' => 'mypassword',
                'incoming_all_unread' => 'All',
            ],
            $mockMailer,
            $mockMessageRepo,
            $mockConfigRepo
        );

        $mockImapOpen = PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_open');
        $mockImapOpen
            ->with(
                '{imap.somewhere.com:993/imap/ssl/novalidate-cert}INBOX',
                'someuser',
                'mypassword',
                0,
                1
            )
            ->once()
            ->andReturn('notreallyaconnection');

        $mockImapOpen = PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_check');
        $mockImapOpen
            ->with('notreallyaconnection')
            ->once()
            ->andReturn((object) ['Nmsgs' => 100]);

        $mockImapClose = PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_close');
        $mockImapClose
            ->with('notreallyaconnection')
            ->once();
        $mockErrors = PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_errors');
        $mockAlerts = PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_alerts');

        $mockFetchOverview = PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_fetch_overview');
        $mockFetchOverview
            ->with('notreallyaconnection', '713:912', FT_UID)
            ->once()
            ->andReturn([
                (object) [
                    'uid' => 1,
                    'from' => 'from@ushahidi.app',
                    'subject' => 'Message 1',
                ],
                (object) [
                    'uid' => 5,
                    'from' => 'from2@ushahidi.app',
                    'subject' => 'Message 5',
                ],
                (object) [
                    'uid' => 7,
                    'from' => 'from3@ushahidi.app',
                    'subject' => 'Message 7',
                ],
            ]);

        $mockFetchStructure = PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_fetchstructure');
        // Call for first email
        $mockFetchStructure
            ->with('notreallyaconnection', 1, FT_UID)
            ->once()
            ->andReturn(
                (object) [
                    'parts' => [
                        11 => (object) [
                            'subtype' => 'HTML',
                        ],
                        111 => (object) [
                            'subtype' => 'PLAIN',
                        ],
                    ],
                ]
            );
        // Handle call for 2nd message
        $mockFetchStructure
            ->once()
            ->with('notreallyaconnection', 5, FT_UID)
            ->andReturn(
                (object) [
                    'parts' => [
                        55 => (object) [
                            'subtype' => 'HTML',
                        ],
                    ],
                ]
            );
        // Handle call for 3rd message
        $mockFetchStructure
            ->once()
            ->with('notreallyaconnection', 7, FT_UID)
            ->andReturn(
                (object) [
                    'parts' => [
                        77 => (object) [
                            'subtype' => 'PLAIN',
                        ],
                    ],
                ]
            );

        $mockFetchBody = PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_fetchbody');
        // Handle first message HTML
        $mockFetchBody
            ->with('notreallyaconnection', 1, 11, FT_UID)
            ->once()
            ->andReturn('Some HTML');
        // ... and plain text
        $mockFetchBody
            ->once()
            ->with('notreallyaconnection', 1, 111, FT_UID)
            ->andReturn('Plain text');

        // Handle 2nd message HTML
        $mockFetchBody
            ->once()
            ->with('notreallyaconnection', 5, 55, FT_UID)
            ->andReturn('HTML 2');

        // Handle 3rd message plaintext
        $mockFetchBody
            ->once()
            ->with('notreallyaconnection', 7, 77, FT_UID)
            ->andReturn('Plain text 3');

        $mockQPrint = PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_qprint');
        $mockQPrint
            ->with('Some HTML')
            ->once()
            ->andReturn('Some HTML');
        $mockQPrint
            ->once()
            ->with('HTML 2')
            ->andReturn('HTML 2');
        $mockQPrint
            ->once()
            ->with('Plain text 3')
            ->andReturn('Plain text 3');

        $messages = $email->fetch();

        $this->assertEquals([
            [
                'type' => 'email',
                'contact_type' => 'email',
                'from' => 'from@ushahidi.app',
                'message' => 'Some HTML',
                'to' => null,
                'title' => 'Message 1',
                'data_source_message_id' => 1,
                'additional_data' => [],
                'datetime' => null,
            ],
            [
                'type' => 'email',
                'contact_type' => 'email',
                'from' => 'from2@ushahidi.app',
                'message' => 'HTML 2',
                'to' => null,
                'title' => 'Message 5',
                'data_source_message_id' => 5,
                'additional_data' => [],
                'datetime' => null,
            ],
            [
                'type' => 'email',
                'contact_type' => 'email',
                'from' => 'from3@ushahidi.app',
                'message' => 'Plain text 3',
                'to' => null,
                'title' => 'Message 7',
                'data_source_message_id' => 7,
                'additional_data' => [],
                'datetime' => null,
            ],
        ], $messages);
    }
}
