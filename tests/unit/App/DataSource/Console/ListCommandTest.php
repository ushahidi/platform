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
        $this->app->make('datasources')->setEnabledSources([
            'email' => false,
            'frontlinesms' => true,
            'nexmo' => false,
            'twilio' => true,
            'twitter' => false,
            'smssync' => true,
        ]);
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
