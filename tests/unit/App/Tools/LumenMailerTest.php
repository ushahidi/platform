<?php

/**
 * Unit tests for Lumen implementation of Ushahidi\Core\Tool\Mailer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\Tools;

use Ushahidi\App\Tools\LumenMailer;
// use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Mockery as M;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class LumenMailerTest extends TestCase
{

    public function testSendWithMultisite()
    {
        config([
            'mail.pretend' => true
        ]);

        config([
            'multisite.email' => 'deploy@multisite.ushahidi.app'
        ]);

        $illuminateMailer = M::spy(app('mailer'));

        $mailer = new LumenMailer(
            $illuminateMailer,
            [
                'name' => 'TestDeploy',
                'email' => 'test@ushahidi.app'
            ],
            'https://ushahidi.app/'
        );

        $mailer->send('noone@ushahidi.com', 'Resetpassword', [
            'token' => 'abc123'
        ]);

        $illuminateMailer->shouldHaveReceived('send')
            ->once()
            ->with(
                'emails/forgot-password',
                M::on(function ($data) {
                    $this->assertArrayHasKey('site_name', $data);
                    $this->assertArrayHasKey('token', $data);
                    $this->assertArrayHasKey('client_url', $data);

                    return true;
                }),
                M::on(function (\Closure $closure) {
                    $mock = M::mock('Illuminate\Mailer\Message');
                    $mock->shouldReceive('to')->once()->with('noone@ushahidi.com')
                         ->andReturn($mock); // simulate the chaining
                    $mock->shouldReceive('from')->once()->with('deploy@multisite.ushahidi.app', 'TestDeploy')
                         ->andReturn($mock); // simulate the chaining
                    $mock->shouldReceive('subject')->once()->with('TestDeploy: Password reset')
                         ->andReturn($mock); // simulate the chaining

                    $closure($mock);
                    return true;
                })
            );
    }

    public function testSendWithSiteEmail()
    {
        config([
            'mail.pretend' => true
        ]);

        config([
            'multisite.email' => false
        ]);

        $illuminateMailer = M::spy(app('mailer'));

        $mailer = new LumenMailer(
            $illuminateMailer,
            [
                'name' => 'TestDeploy',
                'email' => 'test@ushahidi.app'
            ],
            'https://ushahidi.app/'
        );

        $mailer->send('noone@ushahidi.com', 'Resetpassword', [
            'token' => 'abc123'
        ]);

        $illuminateMailer->shouldHaveReceived('send')
            ->once()
            ->with(
                'emails/forgot-password',
                M::on(function ($data) {
                    $this->assertArrayHasKey('site_name', $data);
                    $this->assertArrayHasKey('token', $data);
                    $this->assertArrayHasKey('client_url', $data);

                    return true;
                }),
                M::on(function (\Closure $closure) {
                    $mock = M::mock('Illuminate\Mailer\Message');
                    $mock->shouldReceive('to')->once()->with('noone@ushahidi.com')
                         ->andReturn($mock); // simulate the chaining
                    $mock->shouldReceive('from')->once()->with('test@ushahidi.app', 'TestDeploy')
                         ->andReturn($mock); // simulate the chaining
                    $mock->shouldReceive('subject')->once()->with('TestDeploy: Password reset')
                         ->andReturn($mock); // simulate the chaining

                    $closure($mock);
                    return true;
                })
            );
    }

    public function testSendWithFallbackEmail()
    {

        config([
            'mail.pretend' => true
        ]);

        config([
            'multisite.email' => false
        ]);

        $illuminateMailer = M::spy(app('mailer'));

        $mailer = new LumenMailer(
            $illuminateMailer,
            [
                'name' => 'TestDeploy',
                'email' => false
            ],
            'https://ushahidi.app/'
        );

        $mailer->send('noone@ushahidi.com', 'Resetpassword', [
            'token' => 'abc123'
        ]);

        $illuminateMailer->shouldHaveReceived('send')
            ->once()
            ->with(
                'emails/forgot-password',
                M::on(function ($data) {
                    $this->assertArrayHasKey('site_name', $data);
                    $this->assertArrayHasKey('token', $data);
                    $this->assertArrayHasKey('client_url', $data);

                    return true;
                }),
                M::on(function (\Closure $closure) {
                    $mock = M::mock('Illuminate\Mailer\Message');
                    $mock->shouldReceive('to')->once()->with('noone@ushahidi.com')
                         ->andReturn($mock); // simulate the chaining
                    $mock->shouldNotReceive('from');
                    $mock->shouldReceive('subject')->once()->with('TestDeploy: Password reset')
                         ->andReturn($mock); // simulate the chaining

                    $closure($mock);
                    return true;
                })
            );
    }
}
