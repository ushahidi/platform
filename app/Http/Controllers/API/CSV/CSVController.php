<?php

namespace Ushahidi\App\Http\Controllers\API\CSV;

use Ushahidi\App\Http\Controllers\API\MediaController;
use Illuminate\Http\Request;

/**
 * Ushahidi API CSV Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class CSVController extends MediaController
{
    protected function getResource()
    {
        return 'csv';
    }

    public function import(Request $request)
    {
        /**
         * Step two of import.
         * Support all line endings without manually specifying it
         * (primarily added because of OS9 line endings which do not work by default )
         */
        ini_set('auto_detect_line_endings', 1);

        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'import')
            ->setIdentifiers($this->getRouteParams($request));
            ;

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
