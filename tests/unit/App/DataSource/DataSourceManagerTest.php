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

use Mockery as M;
use Tests\TestCase;
use Ushahidi\App\DataSource\DataSourceManager;
use Ushahidi\App\DataSource\Email\Email;
use Ushahidi\App\DataSource\IncomingAPIDataSource;
use Ushahidi\App\DataSource\Nexmo\Nexmo;
use Ushahidi\App\DataSource\Twitter\Twitter;
use Ushahidi\Core\Entity\Config;
use Ushahidi\Core\Entity\ConfigRepository;

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

        $this->assertInternalType('array', $manager->getSources());
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
                ],
            ]));

        $configRepo->shouldReceive('get')
            ->with('features')
            ->andReturn(new Config([
                'data-providers' => [
                    'nexmo' => true,
                    'twitter' => true,
                    'email' => true,
                ],
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
                    'email' => true,
                ],
            ]));

        $configRepo->shouldReceive('get')
            ->with('features')
            ->andReturn(new Config([
                'data-providers' => [
                    'nexmo' => true,
                ],
            ]));

        $this->assertCount(1, $manager->getEnabledSources());
        $this->assertFalse($manager->isEnabledSource('twitter'));

        $this->expectException(\InvalidArgumentException::class);
        $manager->getEnabledSource('twitter');
    }

    public function testCustomSources()
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
                    'custom-provider' => true,
                ],
            ]));

        $configRepo->shouldReceive('get')
            ->with('features')
            ->andReturn(new Config([
                'data-providers' => [
                    'email' => true,
                    'custom-provider' => true,
                ],
            ]));

        $customSource = function ($config = []) {
            return new class($config) implements IncomingAPIDataSource
            {
                protected $config;

                public function __construct($config)
                {
                    $this->config = $config;
                }

                public function fetch($limit = false)
                {
                }
                public function getName()
                {
                }
                public function getId()
                {
                }
                public function getServices()
                {
                }
                public function getOptions()
                {
                }
                public function getInboundFields()
                {
                }
                public function getInboundFormId()
                {
                }
                public function getInboundFieldMappings()
                {
                }
                public function isUserConfigurable()
                {
                }
            };
        };

        $manager->extend('custom-provider', $customSource);

        $this->assertCount(2, $manager->getEnabledSources());
        $this->assertFalse($manager->isEnabledSource('twitter'));
        $this->assertTrue($manager->isEnabledSource('custom-provider'));

        $class = call_user_func($customSource);
        $this->assertInstanceOf(get_class($class), $manager->getSource('custom-provider'));
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
                    'smssync' => true,
                ],
            ]));

        $configRepo->shouldReceive('get')
            ->with('features')
            ->andReturn(new Config([
                'data-providers' => [
                    'nexmo' => true,
                    'twitter' => true,
                    'email' => true,
                    'smssync' => true,
                ],
            ]));

        $this->assertInstanceOf(Twitter::class, $manager->getSourceForType('twitter'));
        $this->assertInstanceOf(Nexmo::class, $manager->getSourceForType('sms'));
    }
}
