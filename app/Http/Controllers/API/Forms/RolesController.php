<?php

namespace Ushahidi\App\Http\Controllers\API\Forms;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API Forms Roles Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class RolesController extends RESTController {

    protected function getResource()
    {
        return 'form_roles';
    }

    public function index(Request $request)
    {
        $params = $this->getRouteParams($request);
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
            ->setIdentifiers($params)
            ->setFilters($request->query() + [
                'form_id' => $params['form_id']
            ]);

        return $this->prepResponse($this->executeUsecase(), $request);
    }

    public function replace(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'update_collection')
            ->setIdentifiers($this->getRouteParams($request))
            ->setPayload($request->json()->all());

        return $this->prepResponse($this->executeUsecase(), $request);
    }

}
