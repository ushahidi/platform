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

use Dotenv\Dotenv;
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

        $envCheckerMock->shouldReceive('isMissingEnvKey')
            ->with('DB_CONNECTION')
            ->andReturn(true);

        $result = $envCheckerMock->verifyRequirements(false);

        $this->assertArrayHasKey('errors', $result);
        $errors = $result['errors'];
        $this->assertGreaterThanOrEqual(2, count($errors));
        $this->assertContains([
            'message' => 'Required environment variables missing and no environment file found.',
            'explainer' => "Please copy the '.env.example' file into a file named '.env' " .
                        "and set your missing variables."
        ], $errors);
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
                'message' => 'Good job! you have configured your system environment and/or .env file ' .
                            'with all the required keys.',
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
                'message' => 'DB_CONNECTION is missing in the environment or .env file.',
                'explainer' => 'Please set `DB_CONNECTION=mysql` in the environment or .env file.'
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
                'message' => 'DB_CONNECTION is missing in the environment or .env file.',
                'explainer' => 'Please set `DB_CONNECTION=mysql` in the environment or .env file.'
            ],
            [
                'message' => 'DB_USERNAME is missing in the environment or .env file.',
                'explainer' => 'Please set the username to connect to your database in the DB_USERNAME key'
            ]
        ]], $result);
    }
}
