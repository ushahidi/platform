<?php

namespace Ushahidi\App\V3\Http\Controllers\API;

use Ushahidi\App\V3\Http\Controllers\RESTController;

/**
 * Ushahidi API Index Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class IndexController
{
    /**
     * Retrieve a basic information about the API
     *
     * GET /api
     *
     */
    public function index()
    {
        $user = service('session')->getUser();

        return [
            'now'       => date(\DateTime::W3C),
            'version'   => RESTController::version(),
            'user'      => [
                'id'       => $user->id,
                'email'    => $user->email,
                'realname' => $user->realname,
            ],
        ];
    }
}
