<?php

namespace Ushahidi\App\Http\Controllers\API\Posts;

use Illuminate\Http\Request;

/**
 * Ushahidi API Posts Exports Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class ExportController extends PostsController
{

	protected function getResource()
	{
		return 'posts_export';
	}

    public function index(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'export')
            ->setFilters($this->getFilters($request));

        $format = strtolower($request->query('format'));

		if ($format) {
			$this->usecase->setFormatter(service("formatter.entity.post.$format"));
		}

        return $this->prepResponse($this->executeUsecase(), $request);
    }
}
