<?php

namespace Ushahidi\App\Http\Controllers\API\Forms;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API Form Stats Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class StatsController extends RESTController
{

    protected function getResource()
    {
        return 'form_stats';
    }

    public function index(Request $request)
    {
        $params = $this->getRouteParams($request);
        $filters = $this->getRouteParams($request);
        $filters['created_after'] = $request->input('created_after');
        $filters['created_before'] = $request->input('created_before');
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
            ->setIdentifiers($params)
            ->setFormatter(service('formatter.entity.form.stats'))
            ->setFilters($filters);
            // @todo do we need this?
            // ->setFilters($request->query() + [
            //     'form_id' => isset($params['form_id']) ? $params['form_id'] : null
            // ])
            ;

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
