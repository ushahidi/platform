<?php

namespace Ushahidi\App\V3\Http\Controllers\API;

use Ushahidi\App\V3\Http\Controllers\RESTController;

/**
 * Ushahidi API Keys Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class ApiKeysController extends RESTController
{
    protected function getResource()
    {
        return 'apikeys';
    }
}
