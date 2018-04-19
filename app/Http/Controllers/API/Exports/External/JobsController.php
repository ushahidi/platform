<?php

namespace Ushahidi\App\Http\Controllers\API\Exports\External;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API External Webhook Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class JobsController extends RESTController
{

    protected function getResource()
    {
        return 'export_jobs';
    }

    public function index(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
            // Override authorizer
            ->setAuthorizer(service('authorizer.external_auth'))
            ->setFilters($request->query());

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    public function show(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'read')
            // Override authorizer
            ->setAuthorizer(service('authorizer.external_auth'))
            ->setIdentifiers($this->getRouteParams($request));

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    public function update(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'update')
            // Override authorizer
            ->setAuthorizer(service('authorizer.external_auth'))
            ->setIdentifiers($this->getRouteParams($request))
            ->setPayload($request->json()->all());

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
