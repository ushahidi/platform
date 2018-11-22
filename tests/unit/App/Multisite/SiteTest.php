<?php

/**
 * Unit tests for Lumen implementation of Ushahidi\Core\Tool\Mailer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\Multisite;

use Ushahidi\App\Multisite\Site;
use Ushahidi\Core\Entity\Config;
use Ushahidi\Core\Entity\ConfigRepository;
// use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Mockery as M;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class SiteTest extends TestCase
{

    public function testItShouldReturnMultisiteEmail()
    {
        config([
            'multisite.enabled' => true,
            'multisite.email' => 'deploy@multisite.ushahidi.app'
        ]);

        $site = new Site([]);

        $this->assertEquals('deploy@multisite.ushahidi.app', $site->getEmail());
    }

    public function testItShouldReturnSiteEmail()
    {
        config([
            'multisite.enabled' => false,
            'multisite.email' => 'deploy@multisite.ushahidi.app'
        ]);

        // Mock the config repo
        $configRepo = M::mock(ConfigRepository::class);
        // Return email in config
        $configRepo->shouldReceive('get')->with('site')->andReturn(new Config([
            'email' => 'us@site.com'
        ]));
        $this->app->instance(ConfigRepository::class, $configRepo);

        $site = new Site([]);

        $this->assertEquals('us@site.com', $site->getEmail());
    }

    public function testItShouldReturnFallbackEmail()
    {
        config([
            'multisite.enabled' => false,
            'multisite.email' => 'deploy@multisite.ushahidi.app'
        ]);

        // Mock the config repo
        $configRepo = M::mock(ConfigRepository::class);
        // Return empty config
        $configRepo->shouldReceive('get')->with('site')->andReturn(new Config([]));
        $this->app->instance(ConfigRepository::class, $configRepo);

        // Fake the request
        $this->app->instance(
            \Illuminate\Http\Request::class,
            new \Illuminate\Http\Request([], [], [], [], [], ['HTTP_HOST' => 'host.com'])
        );

        $site = new Site([]);

        $this->assertEquals('noreply@host.com', $site->getEmail());
    }
}
