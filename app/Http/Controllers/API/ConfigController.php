<?php

namespace Ushahidi\App\Http\Controllers\API;

use Ushahidi\App\Http\Controllers\RESTController;

/**
 * Ushahidi API Config Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class ConfigController extends RESTController
{

    // protected $_action_map = array
    // (
    //     Http_Request::GET     => 'get',
    //     Http_Request::PUT     => 'put', // Typically Update..
    //     Http_Request::OPTIONS => 'options',
    // );

    protected function getScope()
    {
        return 'config';
    }

    // protected function _is_auth_required()
    // {
    //     if (parent::_is_auth_required())
    //     {
    //         // Completely anonymous access is allowed for (some) GET requests.
    //         // Further checks are made down the stack.
    //         return ($this->request->method() !== Request::GET);
    //     }
    //     return FALSE;
    // }

}
