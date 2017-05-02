<?php

namespace Ushahidi\App\Http\Controllers\API\Posts;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Usecase;

class PostsController extends RESTController {

	/**
	 * @var int Post Parent ID
	 */
	protected $parentId = NULL;

	/**
	 * @var string Post Type
	 */
	protected $postType = 'report';

	// Ushahidi_Rest
	protected function getResource()
	{
		return 'posts';
	}

	protected function getIdentifiers(Request $request)
	{
		return $this->getRouteParams($request) + [
			'type'      => $this->postType
		];
	}

	protected function getFilters(Request $request)
	{
        $params = $this->getRouteParams($request);
		return $request->query() + [
			'type'      => $this->postType,
			'parent'    => isset($params['parent_id']) ? $params['parent_id'] : null,
		];
	}

	protected function getPayload(Request $request)
	{
        $params = $this->getRouteParams($request);
		return $request->json()->all() + [
			'type'      => $this->postType,
			'parent_id' => isset($params['parent_id']) ? $params['parent_id'] : null,
		];
	}

	/**
	 * Create A Post
	 *
	 * POST /api/posts
	 *
	 * @return void
	 */
    public function store(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'create')
            ->setPayload($this->getPayload($request))
			->setIdentifiers($this->getIdentifiers($request));

        return $this->prepResponse($this->executeUsecase(), $request);
    }

	/**
	 * Retrieve All Posts
	 *
	 * GET /api/posts
	 *
	 * @return void
	 */
    public function index(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
            ->setFilters($this->getFilters($request))
			->setIdentifiers($this->getIdentifiers($request));

        return $this->prepResponse($this->executeUsecase(), $request);
    }

    /**
     * Retrieve An Entity
     *
     * GET /api/foo/:id
     *
     * @return void
     */
    public function show(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'read')
            ->setIdentifiers($this->getIdentifiers($request));

        return $this->prepResponse($this->executeUsecase(), $request);
    }

    /**
     * Update An Entity
     *
     * PUT /api/foo/:id
     *
     * @return void
     */
    public function update(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'update')
            ->setIdentifiers($this->getIdentifiers($request))
            ->setPayload($this->getPayload($request));

        return $this->prepResponse($this->executeUsecase(), $request);
    }

    /**
     * Delete An Entity
     *
     * DELETE /api/foo/:id
     *
     * @return void
     */
    public function destroy(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'delete')
            ->setIdentifiers($this->getIdentifiers($request));

        return $this->prepResponse($this->executeUsecase(), $request);
    }

	/**
	 * Retrieve post stats
	 *
	 * GET /api/posts/stats
	 *
	 * @return void
	 */
	public function stats(Request $request)
	{
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'stats')
            ->setFilters($this->getFilters($request))
			// @todo allow injecting formatters based on resource + action
			->setFormatter(service('formatter.entity.post.stats'));

        return $this->prepResponse($this->executeUsecase(), $request);
	}
}
