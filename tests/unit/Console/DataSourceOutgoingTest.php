<?php

/**
 * Tests for datasource:outgoing command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Console;

use Tests\TestCase;
use Mockery as M;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class DataSourceOutgoingTest extends TestCase
{

    public function setUp() {
        parent::setUp();
        // Ensure enabled providers is in a known state
        $this->app->make('datasources')->setEnabledSources([
            'email' => false,
            'frontlinesms' => true,
            'nexmo' => false,
            'twilio' => true,
            'twitter' => false,
            'smssync' => true,
        ]);
    }

    public function testOutgoing()
    {
        $value = $this->artisan('datasource:outgoing', []);

        $this->assertEquals(
"+--------------+-------+
| Source       | Total |
+--------------+-------+
| FrontlineSMS | 0     |
| Twilio       | 0     |
| Email        | 0     |
| Unassigned   | 0     |
+--------------+-------+
", $this->artisanOutput());
    }

    public function testOutgoingAll()
    {
        $value = $this->artisan('datasource:outgoing', ["--all" => true]);

        $this->assertEquals(
"+--------------+-------+
| Source       | Total |
+--------------+-------+
| Email        | 0     |
| FrontlineSMS | 0     |
| Nexmo        | 0     |
| Twilio       | 0     |
| Twitter      | 0     |
| Unassigned   | 0     |
+--------------+-------+
", $this->artisanOutput());
    }

    public function testOutgoingNexmo()
    {
        $value = $this->artisan('datasource:outgoing', ["--source" => "nexmo"]);

        $this->assertEquals(
"+--------+-------+
| Source | Total |
+--------+-------+
| Nexmo  | 0     |
+--------+-------+
", $this->artisanOutput());
    }

}
