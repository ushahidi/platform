<?php

/**
 * Tests for datasource:list command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\DataSource\Console;

use Tests\TestCase;
use Mockery as M;
use Ushahidi\App\DataSource\Console\ListCommand;
use Ushahidi\App\DataSource\DataSourceManager;
use Illuminate\Console\Application as Artisan;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class ListCommandTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        // Ensure enabled providers is in a known state
        // Mock the config repo
        $configRepo = M::mock(\Ushahidi\Core\Entity\ConfigRepository::class);
        $configRepo->shouldReceive('get')->with('data-provider')->andReturn(new \Ushahidi\Core\Entity\Config([
            'providers' => [
                'email' => false,
                'frontlinesms' => true,
                'nexmo' => false,
                'twilio' => true,
                'twitter' => false,
                'smssync' => true,
            ]
        ]));
        $configRepo->shouldReceive('get')->with('features')->andReturn(new \Ushahidi\Core\Entity\Config([
            'data-providers' => [
                'email' => false,
                'frontlinesms' => true,
                'nexmo' => false,
                'twilio' => true,
                'twitter' => false,
                'smssync' => true,
            ]
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
            "+--------------+----------+
| Name         | Services |
+--------------+----------+
| FrontlineSMS | sms      |
| SMSSync      | sms      |
| Twilio       | sms      |
+--------------+----------+
",
            $this->artisanOutput()
        );
    }

    public function testListAll()
    {
        $value = $this->artisan('datasource:list', ["--all" => true]);

        $this->assertEquals(
            "+---------------+----------+
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
",
            $this->artisanOutput()
        );
    }
}
