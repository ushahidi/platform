<?php

/**
 * Tests for DataSourceManager class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Console;

use Tests\TestCase;
use Mockery as M;
use Ushahidi\App\DataSource\Email\Email;
use phpmock\mockery\PHPMockery;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class EmailDataSourceTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testSend()
    {
        $mockMailer = M::mock(\Illuminate\Contracts\Mail\Mailer::class);

        $email = new Email(
            [],
            $mockMailer,
            [
                'name' => 'TestDeploy',
                'email' => 'test@ushahidi.app'
            ],
            'https://ushahidi.app/'
        );

        $mockMailer->shouldReceive('send')->with(
            'emails/outgoing-message',
            [
                'message' => 'A message',
                'site_url' => 'https://ushahidi.app/'
            ],
            M::on(function (\Closure $closure) {
                $mock = M::mock(\Illuminate\Mailer\Message::class);
                $mock->shouldReceive('to')->once()->with('test@ushahidi.com')
                     ->andReturn($mock); // simulate the chaining
                $mock->shouldReceive('from')->once()->with('test@ushahidi.app', 'TestDeploy')
                     ->andReturn($mock); // simulate the chaining
                $mock->shouldReceive('subject')->once()->with('A title')
                     ->andReturn($mock); // simulate the chaining

                $closure($mock);
                return true;
            })
        );

        $response = $email->send('test@ushahidi.com', "A message", "A title");

        $this->assertInternalType('array', $response);
        $this->assertEquals('sent', $response[0]);
        $this->assertEquals(false, $response[1]);
    }

    public function testFetch()
    {
        $mockMailer = M::mock(\Illuminate\Contracts\Mail\Mailer::class);

        $email = new Email(
            [
                'incoming_type' => 'imap',
                'incoming_server' => 'imap.somewhere.com',
                'incoming_port' => '993',
                'incoming_security' => 'ssl',
                'incoming_username' => 'someuser',
                'incoming_password' => 'mypassword',
            ],
            $mockMailer,
            [
                'name' => 'TestDeploy',
                'email' => 'test@ushahidi.app'
            ],
            'https://ushahidi.app/'
        );

        $mockImapOpen = PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_open");
        $mockImapOpen
            ->with(
                '{imap.somewhere.com:993/imap/ssl}INBOX',
                'someuser',
                'mypassword'
            )
            ->andReturn('notreallyaconnection');

        $mockImapClose = PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_close");
        $mockImapClose
            ->with('notreallyaconnection');
        $mockErrors = PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_errors");

        $mockFetchOverview = PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_fetch_overview");
        $mockFetchOverview
            ->with('notreallyaconnection', '1:200', FT_UID)
            ->andReturn([
                (object)[
                    'uid' => 1,
                    'from' => 'from@ushahidi.app',
                    'subject' => "Message 1"
                ],
                (object)[
                    'uid' => 5,
                    'from' => 'from2@ushahidi.app',
                    'subject' => "Message 5"
                ],
                (object)[
                    'uid' => 7,
                    'from' => 'from3@ushahidi.app',
                    'subject' => "Message 7"
                ]
            ]);

        $mockFetchStructure = PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_fetchstructure");
        // Call for first email
        $mockFetchStructure
            ->with('notreallyaconnection', 1, FT_UID)
            ->andReturn(
                (object)[
                    'parts' => [
                        11 => (object)[
                            'subtype' => 'HTML'
                        ],
                        111 => (object)[
                            'subtype' => 'PLAIN'
                        ]
                    ]
                ]
            );
        // Handle call for 2nd message
        $mockFetchStructure
            ->getMock()
                ->shouldReceive(\phpmock\integration\MockDelegateFunctionBuilder::METHOD)
            ->with('notreallyaconnection', 5, FT_UID)
            ->andReturn(
                (object)[
                    'parts' => [
                        55 => (object)[
                            'subtype' => 'HTML'
                        ]
                    ]
                ]
            );
        // Handle call for 3rd message
        $mockFetchStructure
            ->getMock()
                ->shouldReceive(\phpmock\integration\MockDelegateFunctionBuilder::METHOD)
            ->with('notreallyaconnection', 7, FT_UID)
            ->andReturn(
                (object)[
                    'parts' => [
                        77 => (object)[
                            'subtype' => 'PLAIN'
                        ]
                    ]
                ]
            );

        $mockFetchBody = PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_fetchbody");
        // Handle first message HTML
        $mockFetchBody
            ->with('notreallyaconnection', 1, 11, FT_UID)
            ->andReturn('Some HTML');
        // ... and plain text
        $mockFetchBody
            ->getMock()
                ->shouldReceive(\phpmock\integration\MockDelegateFunctionBuilder::METHOD)
            ->with('notreallyaconnection', 1, 111, FT_UID)
            ->andReturn('Plain text');


        // Handle 2nd message HTML
        $mockFetchBody
            ->getMock()
                ->shouldReceive(\phpmock\integration\MockDelegateFunctionBuilder::METHOD)
            ->with('notreallyaconnection', 5, 55, FT_UID)
            ->andReturn('HTML 2');


        // Handle 3rd message plaintext
        $mockFetchBody
            ->getMock()
                ->shouldReceive(\phpmock\integration\MockDelegateFunctionBuilder::METHOD)
            ->with('notreallyaconnection', 7, 77, FT_UID)
            ->andReturn('Plain text 3');

        $mockQPrint = PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_qprint");
        $mockQPrint
            ->with('Some HTML')
            ->andReturn('Some HTML');
        $mockQPrint
            ->getMock()
                ->shouldReceive(\phpmock\integration\MockDelegateFunctionBuilder::METHOD)
            ->with('HTML 2')
            ->andReturn('HTML 2');
        $mockQPrint
            ->getMock()
                ->shouldReceive(\phpmock\integration\MockDelegateFunctionBuilder::METHOD)
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
                'data_source_message_id' => 1
            ],
            [
                'type' => 'email',
                'contact_type' => 'email',
                'from' => 'from2@ushahidi.app',
                'message' => 'HTML 2',
                'to' => null,
                'title' => 'Message 5',
                'data_source_message_id' => 5
            ],
            [
                'type' => 'email',
                'contact_type' => 'email',
                'from' => 'from3@ushahidi.app',
                'message' => 'Plain text 3',
                'to' => null,
                'title' => 'Message 7',
                'data_source_message_id' => 7,
            ],
        ], $messages);
    }

    public function tearDown()
    {
        M::close();
    }
}
