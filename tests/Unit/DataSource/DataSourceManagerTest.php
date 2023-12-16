<?php

/**
 * Tests for DataSourceManager class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\DataSource;

use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\Core\Entity\Config;
use Ushahidi\Tests\CustomSource;
use Ushahidi\DataSource\Email\Email;
use Ushahidi\DataSource\Nexmo\Nexmo;
use Ushahidi\DataSource\Twitter\Twitter;
use Ushahidi\DataSource\DataSourceManager;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;

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
        \Illuminate\Support\Facades\Config::set('cache.default', 'array');

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
        \Illuminate\Support\Facades\Config::set('cache.default', 'array');

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
        \Illuminate\Support\Facades\Config::set('cache.default', 'array');

        $configRepo = M::mock(ConfigRepository::class);
        $manager = new DataSourceManager($configRepo);

        $configRepo->shouldReceive('get')
            ->with('data-provider')
            ->andReturn(new Config([
                'providers' => [
                    'nexmo' => true,
                    'twitter' => true,
                    'email' => true,
                    'custom-1' => true,
                    'custom-2' => true,
                ],
            ]));

        $configRepo->shouldReceive('get')
            ->with('features')
            ->andReturn(new Config([
                'data-providers' => [
                    'email' => true,
                    'custom-1' => true,
                    'custom-2' => true,
                ],
            ]));

        $customSourceCallback = function ($config = []) {
            return new CustomSource($config);
        };

        $manager->extend('custom-1', CustomSource::class);

        $manager->extend('custom-2', CustomSource::class, $customSourceCallback);

        $this->assertCount(3, $manager->getEnabledSources());
        $this->assertFalse($manager->isEnabledSource('twitter'));
        $this->assertTrue($manager->isEnabledSource('custom-1'));
        $this->assertTrue($manager->isEnabledSource('custom-2'));
        $this->assertInstanceOf(CustomSource::class, $manager->getSource('custom-1'));
        $this->assertInstanceOf(CustomSource::class, $manager->getSource('custom-2'));
    }

    public function testGetSourceForType()
    {
        \Illuminate\Support\Facades\Config::set('cache.default', 'array');

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
