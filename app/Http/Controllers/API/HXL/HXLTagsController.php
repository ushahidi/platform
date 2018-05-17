<?php

namespace Ushahidi\App\Http\Controllers\API\HXL;

use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ushahidi\App\Http\Controllers\RESTController;

/**
 * HXL Tags controller
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application\Controllers
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class HXLTagsController extends RESTController
{

    protected function getResource()
    {
        return 'hxl_tags';
    }
}
