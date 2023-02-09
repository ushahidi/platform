<?php

namespace Ushahidi\Modules\V3\Http\Controllers\API\Exports;

use Illuminate\Http\Request;
use Ushahidi\Modules\V3\Http\Controllers\RESTController;

/**
 * Ushahidi API Export Jobs Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class JobsController extends RESTController
{
    protected function getResource()
    {
        return 'export_jobs';
    }
}
