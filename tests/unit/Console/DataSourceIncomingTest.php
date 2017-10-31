<?php

/**
 * Tests for datasource:incoming command
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
class DataSourceIncomingTest extends TestCase
{

    public function testList()
    {
        $value = $this->artisan('datasource:incoming', []);

        $this->assertEquals(
"+--------------+-------+
| Source       | Total |
+--------------+-------+
| FrontlineSMS |       |
| SMSSync      |       |
| Twilio       |       |
+--------------+-------+
", $this->artisanOutput());
    }

}
