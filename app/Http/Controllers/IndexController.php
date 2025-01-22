<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

/**
 * Ushahidi API Index Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class IndexController extends Controller
{
    /**
     * Retrieve a basic information about the API
     *
     * GET /
     *
     * @return void
     */
    public function index()
    {
        return [
            'now' => date(\DateTime::W3C),
        ];
    }
}
