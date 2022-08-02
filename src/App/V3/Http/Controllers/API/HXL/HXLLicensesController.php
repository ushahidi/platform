<?php

namespace Ushahidi\App\V3\Http\Controllers\API\HXL;

use Illuminate\Http\Request;
use Ushahidi\App\V3\Http\Controllers\Controller;
use Ushahidi\App\V3\Http\Controllers\RESTController;

/**
 * HXLLicensesController
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class HXLLicensesController extends RESTController
{
    protected function getResource()
    {
        return 'hxl_licenses';
    }
}
