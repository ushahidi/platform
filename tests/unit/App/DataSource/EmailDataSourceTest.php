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

    }

    public function tearDown()
    {
        M::close();
    }
}
