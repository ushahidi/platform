<?php

/**
 * Tests for datasource:outgoing command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\DataSource\Console;

use Tests\TestCase;
use Mockery as M;
use Ushahidi\App\DataSource\Console\OutgoingCommand;
use Ushahidi\App\DataSource\DataSourceManager;
use Ushahidi\App\DataSource\DataSourceStorage;
use Illuminate\Console\Application as Artisan;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class OutgoingCommandTest extends TestCase
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
        $this->app->instance(\Ushahidi\Core\Entity\ConfigRepository::class, $configRepo);

        // Reinsert command with mocks
        $commands = new OutgoingCommand(new DataSourceManager($configRepo), $this->app->make(DataSourceStorage::class));
        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->add($commands);
        });
    }

    public function testOutgoing()
    {
        $value = $this->artisan('datasource:outgoing', []);

        $this->assertRegExp(
            "/\+--------------\+-------\+
| Source       | Total |
\+--------------\+-------\+
| FrontlineSMS | [0-9]*     |
| Twilio       | [0-9]*     |
| Email        | [0-9]*     |
| Unassigned   | [0-9]*     |
\+--------------\+-------\+
/",
            $this->artisanOutput()
        );
    }

    public function testOutgoingAll()
    {
        $value = $this->artisan('datasource:outgoing', ["--all" => true]);

        $this->assertRegExp(
            "/\+--------------\+-------\+
| Source       | Total |
\+--------------\+-------\+
| Email        | [0-9]*     |
| FrontlineSMS | [0-9]*     |
| Nexmo        | [0-9]*     |
| Twilio       | [0-9]*     |
| Twitter      | [0-9]*     |
| Unassigned   | [0-9]*     |
\+--------------\+-------\+
/",
            $this->artisanOutput()
        );
    }

    public function testOutgoingNexmo()
    {
        $value = $this->artisan('datasource:outgoing', ["--source" => "nexmo"]);

        $this->assertRegExp(
            "/\+--------\+-------\+
| Source | Total |
\+--------\+-------\+
| Nexmo  | [0-9]*     |
\+--------\+-------\+
/",
            $this->artisanOutput()
        );
    }
}
