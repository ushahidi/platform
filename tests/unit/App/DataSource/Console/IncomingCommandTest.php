<?php

/**
 * Tests for datasource:incoming command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\DataSource\Console;

use Tests\TestCase;
use Mockery as M;
use phpmock\mockery\PHPMockery;

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
        $this->app->make('datasources')->setEnabledSources([
            'email' => false,
            'frontlinesms' => true,
            'nexmo' => false,
            'twilio' => true,
            'twitter' => true,
            'smssync' => true,
        ]);

        PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_open");
        PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_close");
        PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_errors");
        PHPMockery::mock("Ushahidi\App\DataSource\Email", "imap_alerts");
    }

    public function testIncoming()
    {
        $value = $this->artisan('datasource:incoming', []);

        $this->assertEquals(
            "+---------+-------+
| Source  | Total |
+---------+-------+
| Twitter | 0     |
+---------+-------+
",
            $this->artisanOutput()
        );
    }

    public function testIncomingAll()
    {
        $value = $this->artisan('datasource:incoming', ["--all" => true]);

        $this->assertEquals(
            "+---------+-------+
| Source  | Total |
+---------+-------+
| Email   | 0     |
| Twitter | 0     |
+---------+-------+
",
            $this->artisanOutput()
        );
    }

    public function testIncomingTwitter()
    {
        $value = $this->artisan('datasource:incoming', ["--source" => "twitter"]);

        $this->assertEquals(
            "+---------+-------+
| Source  | Total |
+---------+-------+
| Twitter | 0     |
+---------+-------+
",
            $this->artisanOutput()
        );
    }
}
