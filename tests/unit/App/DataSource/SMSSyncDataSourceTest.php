<?php

/**
 * Tests for SMSSync class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\DataSource;

use Tests\TestCase;
use Mockery as M;

use Ushahidi\App\DataSource\SMSSync\SMSSync;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class SMSSyncDataSourceTest extends TestCase
{
    public function testVerifySecret()
    {
        $smssync = new SMSSync([
            'secret' => "a secret",
        ]);

        $this->assertTrue($smssync->verifySecret('a secret'));
        $this->assertFalse($smssync->verifySecret('notsecret'));

        $twilio = new SMSSync([]);

        $this->assertFalse($smssync->verifySecret('secret'));
    }
}
