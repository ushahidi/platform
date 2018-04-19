<?php

namespace Ushahidi\App\Http\Controllers\API\Exports\External;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API External Export Jobs Post Count Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class CountController extends RESTController
{

    protected function getResource()
    {
        return 'export_jobs';
    }

    public function show(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            // Override action
            ->get($this->getResource(), 'post-count')
            // Override authorizer
            ->setAuthorizer(service('authorizer.external_auth'))
            ->setIdentifiers($this->getRouteParams($request));

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
