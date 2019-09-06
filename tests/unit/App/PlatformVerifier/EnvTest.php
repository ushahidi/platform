<?php

/**
 * Tests for Env tester
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\PlatformVerifier;

use Tests\TestCase;
use Mockery as M;
use Ushahidi\App\PlatformVerifier\Env as EnvironmentVerifier;

use Ushahidi\App\Tools\OutputText;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Illuminate\Support\Facades\DB as DB;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class EnvTest extends TestCase
{

    public function testMissingEnvFileError()
    {
        parent::setUp();
        $envCheckerMock = M::mock('\Ushahidi\App\PlatformVerifier\Env', 'envExists')->makePartial();

        $envCheckerMock->shouldReceive('envExists')
            ->andReturn(false);

        $result = $envCheckerMock->verifyRequirements(false);

        $this->assertEquals(['errors' => [
            [
                'message' => 'No environment file found. Please copy the .env.example file to create a new .env file.',
                'explainer' => ''
            ]
        ]], $result);
    }

    public function testSuccessEnvKeys()
    {
        parent::setUp();
        $envCheckerMock = M::mock('\Ushahidi\App\PlatformVerifier\Env[envExists,isMissingEnvKey]')->makePartial();

        $envCheckerMock->shouldReceive('envExists')
            ->andReturn(true);

        $result = $envCheckerMock->verifyRequirements(false);

        $this->assertEquals(['success' => [
            [
                'message' => 'Good job! you have configured your .ENV file with all the required keys.',
                'explainer' => null
            ]
        ]], $result);
    }
    public function testMissingEnvVarsError()
    {
        parent::setUp();
        $envCheckerMock = M::mock('\Ushahidi\App\PlatformVerifier\Env[envExists,isMissingEnvKey]')->makePartial();

        $envCheckerMock->shouldReceive('envExists')
            ->andReturn(true);

        $envCheckerMock->shouldReceive('isMissingEnvKey')
            ->with('DB_CONNECTION')
            ->andReturn(true);

        $result = $envCheckerMock->verifyRequirements(false);

        $this->assertEquals(['errors' => [
            [
                'message' => 'DB_CONNECTION is missing from your .env file.',
                'explainer' => 'Please set `DB_CONNECTION=mysql` in the .env file.'
            ]
        ]], $result);
    }
    public function testMultipleMissingEnvVarsError()
    {
        parent::setUp();
        $envCheckerMock = M::mock('\Ushahidi\App\PlatformVerifier\Env[envExists,isMissingEnvKey]')->makePartial();

        $envCheckerMock->shouldReceive('envExists')
            ->andReturn(true);

        $envCheckerMock->shouldReceive('isMissingEnvKey')
            ->with('DB_CONNECTION')
            ->andReturn(true);

        $envCheckerMock->shouldReceive('isMissingEnvKey')
            ->with('DB_USERNAME')
            ->andReturn(true);

        $result = $envCheckerMock->verifyRequirements(false);
        $this->assertEquals(['errors' => [
            [
                'message' => 'DB_CONNECTION is missing from your .env file.',
                'explainer' => 'Please set `DB_CONNECTION=mysql` in the .env file.'
            ],
            [
                'message' => 'DB_USERNAME is missing from your .env file.',
                'explainer' => 'Please set the username to connect to your database in the DB_USERNAME key'
            ]
        ]], $result);
    }
}
