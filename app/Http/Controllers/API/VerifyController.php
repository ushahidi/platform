<?php

/**
 * Ushahidi REST Base Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Http\Controllers\API;

use Ushahidi\App\Http\Controllers\RESTController;

use Ushahidi\Factory\UsecaseFactory;
use Illuminate\Http\Request;
use League\OAuth2\Server\Exception\OAuth2Exception;
use League\OAuth2\Server\Exception\MissingAccessTokenException;
use Ushahidi\App\Exceptions\ValidationException;
use Ushahidi\App\Multisite\MultisiteManager;

class VerifyController extends RESTController
{

    private $errors = [
        // SQLSTATE[HY000] [2002] Connection refused
        '2002' => 'Check that your MySQL server is installed and running, ' .
                    'and that the right DB_HOST and DB_PORT are set up in the .env file',
        '1049' => 'Check that the database in the .env file variable DB_DATABASE exists' .
                    'and that the database user in DB_USERNAME has permissions to access it.',
        '1045' => 'Check that DB_USERNAME, DB_PASSWORD and DB_USERNAME are correct in the .env file. ' .
                    'Verify that accessing mysql through a CLI with `mysql -u YOUR_DB_USERNAME -p ' .
                    'YOUR_DB_NAME and entering the password on prompt results in a successful connection to mysql.'
    ];
    protected function getResource()
    {
        return 'verifier';
    }

    /**
     * @var array List of HTTP methods which may be cached
     */
    protected $cacheableMethods = [];

    /**
     * Get current api version
     */
    public static function version()
    {
        return self::$version;
    }

    public function db(\Illuminate\Http\Request $request)
    {
        $output = \Ushahidi\App\PlatformVerifier\Database::verifyRequirements(false);
        return $output;
    }
    public function conf(\Illuminate\Http\Request $request)
    {
        $output = \Ushahidi\App\PlatformVerifier\Env::verifyRequirements(false);
        return $output;
    }
}
