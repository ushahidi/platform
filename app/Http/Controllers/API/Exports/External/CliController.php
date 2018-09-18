<?php

namespace Ushahidi\App\Http\Controllers\API\Exports\External;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API Export Execute CLI Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Log;

class CliController extends RESTController
{

    protected function getResource()
    {
        return 'export_jobs';
    }

    public function show(Request $request)
    {
        $route_params = $this->getRouteParams($request);
        // this is a trick to convert 'false' to falsy (which would be true),
        // 'true' to true, and an unset param to false
        $include_header = json_decode($request->input('include_header', 1)) == true ? 1 : 0;

        // set CLI params to be the payload for the usecase
        $filters = [
            'limit' => $request->input('limit', 0),
            'offset' => $request->input('offset', 0),
            'add_header' => $include_header
        ];
        Log::debug('In CLI Controller' . var_export($filters, true));
        // Get the usecase and pass in authorizer, payload and transformer
        $this->usecase = $this->usecaseFactory->get('posts_export', 'export')
            ->setFilters($filters)
            ->setIdentifiers(['job_id' => $route_params['id']])
            ->setAuthorizer(service('authorizer.export_job'))
            ->setFormatter(service('formatter.entity.post.csv'));

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
