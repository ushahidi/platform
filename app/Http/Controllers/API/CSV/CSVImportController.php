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
        /**
         * Step two of import.
         * Support all line endings without manually specifying it
         * (primarily added because of OS9 line endings which do not work by default )
         */
        ini_set('auto_detect_line_endings', 1);

        // Get payload from CSV repo
        $csv = service('repository.csv')->get($id);

        $fs = service('tool.filesystem');
        $reader = service('filereader.csv');
        $transformer = service('transformer.csv');

        // Read file
        $file = new \SplTempFileObject();
        $contents = $fs->read($csv->filename);
        $file->fwrite($contents);

        // Get records
        // @todo read up to a sensible offset and process the rest later
        $records = $reader->process($file);

        // Set map and fixed values for transformer
        $transformer->setMap($csv->maps_to);
        $transformer->setFixedValues($csv->fixed);

        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'import')
            ->setPayload($records)
            ->setCSV($csv)
            ->setTransformer($transformer);

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
