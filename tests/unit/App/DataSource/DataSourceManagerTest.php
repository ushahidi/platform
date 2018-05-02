<?php

/**
 * Tests for DataSourceManager class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\DataSource;

use Tests\TestCase;
use Mockery as M;

use Ushahidi\App\DataSource\DataSourceManager;
use Ushahidi\App\DataSource\Email\Email;
use Ushahidi\App\DataSource\Twitter\Twitter;
use Ushahidi\App\DataSource\Nexmo\Nexmo;
use Ushahidi\App\DataSource\SMSSync\SMSSync;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class DataSourceManagerTest extends TestCase
{
    public function testAddGetSource()
    {
        $manager = new DataSourceManager($this->app->router);

        $manager->addSource(new Email([]));
        $manager->addSource(new Twitter([]));

        $this->assertInstanceOf(Twitter::class, $manager->getSource('twitter'));
        $this->assertInstanceOf(Email::class, $manager->getSource('email'));

        $this->assertInternalType('array', $manager->getSource());
    }

    public function testEnabledSources()
    {
        $manager = new DataSourceManager($this->app->router);

        $manager->addSource(new Email([]));
        $manager->addSource(new Twitter([]));
        $manager->addSource(new Nexmo([]));

        $manager->setEnabledSources([
            'nexmo' => true
        ]);

        $manager->setAvailableSources([
            'nexmo' => true,
            'twitter' => true,
            'email' => true
        ]);

        $this->assertCount(1, $manager->getEnabledSources());
        $this->assertFalse($manager->getEnabledSources('twitter'));
    }

    public function testAvailableSources()
    {
        $manager = new DataSourceManager($this->app->router);

        $manager->addSource(new Email([]));
        $manager->addSource(new Twitter([]));
        $manager->addSource(new Nexmo([]));

        $manager->setEnabledSources([
            'nexmo' => true,
            'twitter' => true,
            'email' => true
        ]);

        $manager->setAvailableSources([
            'nexmo' => true
        ]);

        $this->assertCount(1, $manager->getEnabledSources());
        $this->assertFalse($manager->getEnabledSources('twitter'));
    }

    public function testGetSourceForType()
    {
        $manager = new DataSourceManager($this->app->router);

        $manager->addSource(new Email([]));
        $manager->addSource(new Twitter([]));
        $manager->addSource(new Nexmo([]));
        $manager->addSource(new SMSSync([]));

        $manager->setEnabledSources([
            'nexmo' => true,
            'twitter' => true,
            'email' => true,
            'smssync' => true
        ]);

        $manager->setAvailableSources([
            'nexmo' => true,
            'twitter' => true,
            'email' => true,
            'smssync' => true
        ]);

        $this->assertInstanceOf(Twitter::class, $manager->getSourceForType('twitter'));
        $this->assertInstanceOf(Nexmo::class, $manager->getSourceForType('sms'));
    }
}
