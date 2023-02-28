<?php

/**
 * Tests for datasource:list command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\DataSource\Console;

use Illuminate\Console\Application as Artisan;
use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Entity\Config;
use Ushahidi\DataSource\Console\ListCommand;
use Ushahidi\DataSource\DataSourceManager;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class ListCommandTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        // Ensure enabled providers is in a known state
        // Mock the config repo
        $configRepo = M::mock(ConfigRepository::class);
        $configRepo->shouldReceive('get')->with('data-provider')->andReturn(new Config([
            'providers' => [
                'email' => false,
                'frontlinesms' => true,
                'nexmo' => false,
                'twilio' => true,
                'twitter' => false,
                'smssync' => true,
            ],
        ]));
        $configRepo->shouldReceive('get')->with('features')->andReturn(new Config([
            'data-providers' => [
                'email' => false,
                'frontlinesms' => true,
                'nexmo' => false,
                'twilio' => true,
                'twitter' => false,
                'smssync' => true,
            ],
        ]));

        // Reinsert command with mocks
        $commands = new ListCommand(new DataSourceManager($configRepo));
        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->add($commands);
        });
    }

    public function testList()
    {
        $value = $this->artisan('datasource:list', []);

        $this->assertEquals(
            '+--------------+----------+
| Name         | Services |
+--------------+----------+
| FrontlineSMS | sms      |
| SMSSync      | sms      |
| Twilio       | sms      |
+--------------+----------+
',
            $this->artisanOutput()
        );
    }

    public function testListAll()
    {
        $value = $this->artisan('datasource:list', ['--all' => true]);

        $this->assertEquals(
            '+---------------+----------+
| Name          | Services |
+---------------+----------+
| Email         | email    |
| OutgoingEmail | email    |
| FrontlineSMS  | sms      |
| Nexmo         | sms      |
| SMSSync       | sms      |
| Twilio        | sms      |
| Twitter       | twitter  |
+---------------+----------+
',
            $this->artisanOutput()
        );
    }
}
