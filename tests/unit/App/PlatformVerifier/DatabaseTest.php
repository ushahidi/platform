<?php
/**
 * File description
 *
 * @tags
 * @phpcs:disable PSR1.Classes.ClassDeclaration
 */

/**
 * Tests for Database tester
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\App\PlatformVerifier;

use Tests\TestCase;
use Mockery as M;
use Ushahidi\App\PlatformVerifier;

use Ushahidi\App\Tools\OutputText;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Illuminate\Support\Facades\DB as DB;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class StubException extends \PDOException
{
    public function __construct()
    {
        parent::__construct();
        $this->code = '2002';
        $this->message = 'Error';
    }
}

class StubExceptionUncovered extends \PDOException
{
    public function __construct()
    {
        parent::__construct();
        $this->code = '2032';
        $this->message = 'Error';
    }
}

class DatabaseTest extends TestCase
{

    public function testConnectionErrorHasExplainer()
    {
        $pdoException = new StubException();
        $connection = M::mock(\Illuminate\Support\Facades\DB::connection('mysql'));
        $connection->shouldReceive('getPdo')->with()->andThrow($pdoException);
        $dbCheck = new \Ushahidi\App\PlatformVerifier\Database();
        $result = $dbCheck->verifyRequirements(false, $connection);
        // Note that we are only checking the explainer for a code (2002), since the message is built in
        $this->assertEquals(['errors' => [
            [
                'message' => 'Error',
                'explainer' => 'Check that your MySQL server is installed and running, ' .
                             'and that the right DB_HOST and DB_PORT are set up in the .env file'
            ]
        ]], $result);
    }

    /**
     * given an unexpected database error, check that the message is the one in the exception
     * and that the application responds with an array of errors as expected
    */
    public function testUncoveredErrorsResponds()
    {
        // Ensure enabled providers is in a known state
        // Mock the config repo
        $pdoException = new StubExceptionUncovered();
        $connection = M::mock(\Illuminate\Support\Facades\DB::connection('mysql'));
        $connection->shouldReceive('getPdo')->with()->andThrow($pdoException);
        $dbCheck = new \Ushahidi\App\PlatformVerifier\Database();
        $result = $dbCheck->verifyRequirements(false, $connection);
        // Message remains the same as the exception , explainer is empty due to not matching a known error
        $this->assertEquals(['errors' => [
            [
                'message' => 'Error',
                'explainer' => ''
            ]
        ]], $result);
    }

    public function testConnectionSuccessMessage()
    {
        // Ensure enabled providers is in a known state
        // Mock the config repo
        $pdoException = new StubException();
        $connection = M::mock(\Illuminate\Support\Facades\DB::connection('mysql'));
        $connection->shouldReceive('getPdo');
        $dbCheck = new \Ushahidi\App\PlatformVerifier\Database();
        $result = $dbCheck->verifyRequirements(false, $connection);
        // Note that we are only checking the explainer for a code (2002), since the message is built in
        $this->assertEquals(['success' => [
            [
                'message' => 'We were able to connect to the DB. Well done!',
                'explainer' => null
            ]
        ]], $result);
    }
}
