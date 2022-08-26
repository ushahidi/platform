<?php

/**
 * Unit tests for Lumen implementation of Ushahidi\Core\Tools\Mailer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Ushahidi\App\Multisite;

use Mockery as M;
use Tests\TestCase;
use Illuminate\Http\Request;
use Ushahidi\App\Multisite\Site;
use Ushahidi\Core\Entity\Config;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;

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
            'multisite.email' => 'deploy@multisite.ushahidi.app',
        ]);

        $site = new Site([], 0);

        $this->assertEquals('deploy@multisite.ushahidi.app', $site->getEmail());
    }

    public function testItShouldReturnSiteEmail()
    {
        config([
            'multisite.enabled' => false,
            'multisite.email' => 'deploy@multisite.ushahidi.app',
        ]);

        // Mock the config repo
        $configRepo = M::mock(ConfigRepository::class, function (M\MockInterface $mock) {
            $mock->shouldReceive('get')->with('site')->andReturn(new Config([
                'email' => 'us@site.com'
            ]));
        });
        $this->instance(ConfigRepository::class, $configRepo);

        $site = new Site([], 0);

        $this->assertEquals('us@site.com', $site->getEmail());
    }

    public function testItShouldReturnFallbackEmail()
    {
        config([
            'multisite.enabled' => false,
            'multisite.email' => 'deploy@multisite.ushahidi.app',
        ]);

        // Mock the config repo
        $configRepo = M::mock(ConfigRepository::class, function (M\MockInterface $mock) {
            $mock->shouldReceive('get')
                ->with('site')
                ->andReturn(new Config(['email' => null]));
        });
        $this->instance(ConfigRepository::class, $configRepo);

        // Fake the request
        $this->instance(
            Request::class,
            new Request([], [], [], [], [], [
                'HTTP_HOST' => 'host.com',
                'SERVER_NAME' => 'host.com'
            ])
        );

        \Illuminate\Support\Facades\Request::swap($this->app->make(Request::class));

        $site = new Site([], 0);

        $this->assertEquals('noreply@host.com', $site->getEmail());
    }
}
