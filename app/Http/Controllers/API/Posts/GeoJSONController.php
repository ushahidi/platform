<?php

namespace Ushahidi\App\Http\Controllers\API\Posts;

use Illuminate\Http\Request;

/**
 * Ushahidi API Posts Streams Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class GeoJSONController extends PostsController {

	public function prepBoundingBox(Request $request)
	{
        $params = $this->getRouteParams($request);

		// If zoom/x/y are passed get bounding box
		$zoom = isset($params['zoom']) ? $params['zoom'] : false;
		$x = isset($params['x']) ? $params['x'] : false;
		$y = isset($params['y']) ? $params['y'] : false;
		if ($zoom !== false AND
			$x !== false AND
			$y !== false)
		{
			$boundingBox = \Util_Tile::tileToBoundingBox($zoom, $x, $y);

			$request->merge(['bbox' => implode(',', $boundingBox->as_array())]);
		}
	}

	// Ushahidi_Rest
	protected function getFilters(Request $request)
	{
		return parent::getFilters($request) + [
			'include_types' => ['point', 'geometry']
		];
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
    	$this->prepBoundingBox($request);

        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
            ->setFilters($this->getFilters($request))
			->setIdentifiers($this->getIdentifiers($request))
			->setFormatter(service('formatter.entity.post.geojsoncollection'));

        return $this->prepResponse($this->executeUsecase(), $request);
    }

	/**
	 * Retrieve A Post
	 *
	 * GET /api/posts/:id
	 *
	 * @return void
	 */
    public function show(Request $request)
    {
    	$this->prepBoundingBox($request);

        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'read')
            ->setIdentifiers($this->getIdentifiers($request))
            ->setFormatter(service('formatter.entity.post.geojson'));

        return $this->prepResponse($this->executeUsecase(), $request);
    }

}
