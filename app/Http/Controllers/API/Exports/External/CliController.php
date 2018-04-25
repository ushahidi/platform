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

class CliController extends RESTController
{

    protected function getResource()
    {
        return 'export_jobs';
    }

    public function show(Request $request)
    {
        // this is a trick to convert 'false' to falsy (which would be true),
        // 'true' to true, and an unset param to false
        $include_header = json_decode($request->input('include_header', 1)) == true ? 1 : 0;

        // Run export command
        $exitCode = Artisan::call('export', [
            '--limit' => $request->input('limit', 0),
            '--offset' => $request->input('offset', 0),
            '--job' => $request->input('id'),
            '--include-header' => $include_header,
        ]);

        // Retrieve the results of rhe export
        // which should be a json formatted string
        // containing information aboutt he file generated and
        // saved by the exporter
        $output = Artisan::output();
        $file_details = json_decode($output, true);

        return $this->prepResponse([
            'results' => $file_details,
        ], $request);
    }
}
