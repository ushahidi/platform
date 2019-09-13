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
use Illuminate\Http\Response;
use League\OAuth2\Server\Exception\OAuth2Exception;
use League\OAuth2\Server\Exception\MissingAccessTokenException;
use Ushahidi\App\Exceptions\ValidationException;
use Ushahidi\App\Multisite\MultisiteManager;

class VerifyController extends RESTController
{

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
        if (!\Ushahidi\App\PlatformVerifier\DebugMode::isEnabled()) {
            return (new Response(null, 204))
                    ->header('X-Ushahidi-Platform-Install-Debug-Mode', 'off');
        }

        $output = new \Ushahidi\App\PlatformVerifier\Database();
        return $output->verifyRequirements(false);
    }
    
    public function conf(\Illuminate\Http\Request $request)
    {
        if (!\Ushahidi\App\PlatformVerifier\DebugMode::isEnabled()) {
            return (new Response(null, 204))
                    ->header('X-Ushahidi-Platform-Install-Debug-Mode', 'off');
        }

        $output = new \Ushahidi\App\PlatformVerifier\Env();
        return $output->verifyRequirements(false);
    }
}
