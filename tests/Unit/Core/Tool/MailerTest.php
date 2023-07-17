<?php

/**
 * Unit tests for Lumen implementation of Ushahidi\Core\Tool\Mailer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\Core\Tool;

use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\Core\Entity\Site;
use Ushahidi\Core\Tool\Mailer;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class MailerTest extends TestCase
{
    public function testSend()
    {
        config([
            'mail.pretend' => true,
        ]);

        // Mock the current site
        $site = M::mock(Site::class);
        $site->shouldReceive('getEmail')->andReturn('siteemail@site.com');
        $site->shouldReceive('getName')->andReturn('The Site');
        $site->shouldReceive('getClientUri')->andReturn('https://site.com');
        $site->shouldReceive('getDbConfig')->andReturn([
            'host' => config('database.connections.mysql.host'),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ]);
        $site->shouldReceive('getId')->andReturn(1);
        $this->app->make('site')->setSite($site);

        $illuminateMailer = M::spy(app('mailer'));

        $mailer = new Mailer(
            $illuminateMailer
        );

        $code = 'abc123';
        $mailer->send('noone@ushahidi.com', 'Resetpassword', [
            'code' => $code,
            'user_name' => 'No One',
            'string' => base64_encode($code),
            'duration' => 30,
        ]);

        $illuminateMailer->shouldHaveReceived('send')
            ->once()
            ->with(
                'emails/forgot-password',
                M::on(function ($data) {
                    $this->assertArrayHasKey('site_name', $data);
                    $this->assertArrayHasKey('reset_code', $data);
                    $this->assertArrayHasKey('client_url', $data);

                    return true;
                }),
                M::on(function (\Closure $closure) {
                    $mock = M::mock('Illuminate\Mailer\Message');
                    $mock->shouldReceive('to')->once()->with('noone@ushahidi.com')
                         ->andReturn($mock); // simulate the chaining
                    $mock->shouldReceive('from')->once()->with('siteemail@site.com', 'The Site')
                         ->andReturn($mock); // simulate the chaining
                    $mock->shouldReceive('subject')->once()->with('The Site: Password reset')
                         ->andReturn($mock); // simulate the chaining

                    $closure($mock);

                    return true;
                })
            );
    }
}
