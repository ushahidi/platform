<?php

namespace Ushahidi\App\Http\Controllers\API\Forms;

use Illuminate\Http\Request;
use Ushahidi\App\Http\Controllers\RESTController;

/**
 * Ushahidi API Forms Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class FormsController extends RESTController
{
    protected function getResource()
    {
        return 'forms';
    }
}