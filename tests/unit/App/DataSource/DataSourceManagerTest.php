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
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Entity\Config;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class DataSourceManagerTest extends TestCase
{
    public function testGetSource()
    {
        $configRepo = M::mock(ConfigRepository::class);
        $manager = new DataSourceManager($configRepo);

        $configRepo->shouldReceive('get')
            ->with('data-provider')
            ->andReturn(new Config([]));

        $this->assertInstanceOf(Twitter::class, $manager->getSource('twitter'));
        $this->assertInstanceOf(Email::class, $manager->getSource('email'));

        $this->assertIsArray($manager->getSources());
    }

    public function testEnabledSources()
    {
        $configRepo = M::mock(ConfigRepository::class);
        $manager = new DataSourceManager($configRepo);

        $configRepo->shouldReceive('get')
            ->with('data-provider')
            ->andReturn(new Config([
                'providers' => [
                    'nexmo' => true,
                ]
            ]));

        $configRepo->shouldReceive('get')
            ->with('features')
            ->andReturn(new Config([
                'data-providers' => [
                    'nexmo' => true,
                    'twitter' => true,
                    'email' => true,
                ]
            ]));

        $this->assertCount(1, $manager->getEnabledSources());
        $this->assertFalse($manager->isEnabledSource('twitter'));

        $this->expectException(\InvalidArgumentException::class);
        $manager->getEnabledSource('twitter');
    }

    public function testAvailableSources()
    {

        $configRepo = M::mock(ConfigRepository::class);
        $manager = new DataSourceManager($configRepo);

        $configRepo->shouldReceive('get')
            ->with('data-provider')
            ->andReturn(new Config([
                'providers' => [
                    'nexmo' => true,
                    'twitter' => true,
                    'email' => true
                ]
            ]));

        $configRepo->shouldReceive('get')
            ->with('features')
            ->andReturn(new Config([
                'data-providers' => [
                    'nexmo' => true
                ]
            ]));

        $this->assertCount(1, $manager->getEnabledSources());
        $this->assertFalse($manager->isEnabledSource('twitter'));

        $this->expectException(\InvalidArgumentException::class);
        $manager->getEnabledSource('twitter');
    }

    public function testGetSourceForType()
    {
        $configRepo = M::mock(ConfigRepository::class);
        $manager = new DataSourceManager($configRepo);

        $configRepo->shouldReceive('get')
            ->with('data-provider')
            ->andReturn(new Config([
                'providers' => [
                    'nexmo' => true,
                    'twitter' => true,
                    'email' => true,
                    'smssync' => true
                ]
            ]));

        $configRepo->shouldReceive('get')
            ->with('features')
            ->andReturn(new Config([
                'data-providers' => [
                    'nexmo' => true,
                    'twitter' => true,
                    'email' => true,
                    'smssync' => true
                ]
            ]));

        $this->assertInstanceOf(Twitter::class, $manager->getSourceForType('twitter'));
        $this->assertInstanceOf(Nexmo::class, $manager->getSourceForType('sms'));
    }
}
