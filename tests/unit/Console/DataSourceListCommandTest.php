<?php

/**
 * Tests for datasource:list command
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
class DataSourceListTest extends TestCase
{

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
", $this->artisanOutput());
    }

}
