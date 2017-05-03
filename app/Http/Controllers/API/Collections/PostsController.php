<?php

namespace Ushahidi\App\Http\Controllers\API\Collections;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API Collections Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class PostsController extends RestController
{
	protected function getResource()
	{
		return 'sets_posts';
	}


    /**
     * Add a post to a collection
     *
     * POST /api/v3/collections/:id/posts
     *
     * @return void
     */
    public function store(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'create')
            ->setPayload($request->json()->all())
			// Send through parent collection id
			->setIdentifiers($this->getRouteParams($request));

        return $this->prepResponse($this->executeUsecase(), $request);
    }

    /**
     * Get posts in a collection
     *
     * GET /api/v3/collections/:id/posts
     *
     * @return void
     */
    public function index(Request $request)
    {
        $params = $this->getRouteParams($request);
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
			// And add parent collection id to the filters
            ->setFilters($request->query() + [
                'set_id' => isset($params['set_id']) ? $params['set_id'] : null
			])
			// Send through parent collection id
			->setIdentifiers($params);

        return $this->prepResponse($this->executeUsecase(), $request);
    }
}
