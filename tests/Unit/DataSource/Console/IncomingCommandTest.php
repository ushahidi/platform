<?php

/**
 * Tests for datasource:incoming command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\DataSource\Console;

use Illuminate\Console\Application as Artisan;
use Mockery as M;
use phpmock\mockery\PHPMockery;
use Ushahidi\Tests\TestCase;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;
use Ushahidi\Core\Entity\Config;
use Ushahidi\DataSource\Console\IncomingCommand;
use Ushahidi\DataSource\DataSourceManager;
use Ushahidi\DataSource\DataSourceStorage;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class IncomingCommandTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        // Ensure enabled providers is in a known state
        // Mock the config repo
        // unset($this->app->availableBindings[ConfigRepository::class]);
        $configRepo = M::mock(ConfigRepository::class);
        $configRepo->shouldReceive('get')->with('data-provider')->andReturn(new Config([
            'providers' => [
                'email' => false,
                'frontlinesms' => true,
                'nexmo' => false,
                'twilio' => true,
                'twitter' => true,
                'smssync' => true,
            ],
        ]));
        $configRepo->shouldReceive('get')->with('features')->andReturn(new Config([
            'data-providers' => [
                'email' => false,
                'frontlinesms' => true,
                'nexmo' => false,
                'twilio' => true,
                'twitter' => true,
                'smssync' => true,
            ],
        ]));
        $configRepo->shouldReceive('get')->with('twitter')->andReturn(new Config([]));
        $this->app->instance(ConfigRepository::class, $configRepo);

        // Reinsert command with mocks
        $commands = new IncomingCommand(new DataSourceManager($configRepo), $this->app->make(DataSourceStorage::class));
        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->add($commands);
        });

        PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_open');
        PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_close');
        PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_errors');
        PHPMockery::mock("Ushahidi\DataSource\Email", 'imap_alerts');
    }

    public function testIncoming()
    {
        $value = $this->artisan('datasource:incoming', []);

        $this->assertEquals(
            '+---------+-------+
| Source  | Total |
+---------+-------+
| Twitter | 0     |
+---------+-------+
',
            $this->artisanOutput()
        );
    }

    public function testIncomingAll()
    {
        $value = $this->artisan('datasource:incoming', ['--all' => true]);

        $this->assertEquals(
            '+---------+-------+
| Source  | Total |
+---------+-------+
| Email   | 0     |
| Twitter | 0     |
+---------+-------+
',
            $this->artisanOutput()
        );
    }

    public function testIncomingTwitter()
    {
        $value = $this->artisan('datasource:incoming', ['--source' => 'twitter']);

        $this->assertEquals(
            '+---------+-------+
| Source  | Total |
+---------+-------+
| Twitter | 0     |
+---------+-------+
',
            $this->artisanOutput()
        );
    }
}
