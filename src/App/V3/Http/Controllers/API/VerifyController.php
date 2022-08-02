<?php

/**
 * Ushahidi REST Base Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Http\Controllers\API;

use Illuminate\Http\Response;
use Ushahidi\App\PlatformVerifier\DebugMode;
use Ushahidi\App\V3\Http\Controllers\RESTController;

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

    public function db()
    {
        if (! DebugMode::isEnabled()) {
            return (new Response(null, 204))
                    ->header('X-Ushahidi-Platform-Install-Debug-Mode', 'off');
        }

        $output = new \Ushahidi\App\PlatformVerifier\Database();

        return $output->verifyRequirements(false);
    }

    public function conf()
    {
        if (! DebugMode::isEnabled()) {
            return (new Response(null, 204))
                    ->header('X-Ushahidi-Platform-Install-Debug-Mode', 'off');
        }

        $output = new \Ushahidi\App\PlatformVerifier\Env();

        return $output->verifyRequirements(false);
    }
}
