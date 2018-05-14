<?php

namespace Ushahidi\App\Http\Controllers\API\Users;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API User SettingsTags Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class SettingsController extends RESTController
{
    protected function getResource()
    {
        return 'user_settings';
    }

    public function index(Request $request)
    {
        $params = $this->getRouteParams($request);
        
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
            ->setFilters($request->query() + [
                'user_id' => $params['user_id']
            ]);

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
