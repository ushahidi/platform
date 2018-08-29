<?php

namespace Ushahidi\App\Http\Controllers\API\CSV;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API CSV Import
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class CSVImportController extends RestController
{
    protected function getResource()
    {
        return 'posts';
    }

    public function store(Request $request, $id = null)
    {
        // Get payload from CSV repo
        $csv = service('repository.csv')->get($id);

        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'import')
            ->setCSV($csv);
        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
