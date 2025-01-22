<?php

namespace Ushahidi\Modules\V3\Http\Controllers\API\Posts;

use Illuminate\Http\Request;
/**
 * Ushahidi API Posts Streams Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\BoundingBox;
use Ushahidi\Core\Tool\Tile;

class GeoJSONController extends PostsController
{
    public function prepBoundingBox(Request $request)
    {
        $params = $this->getRouteParams($request);

        // If zoom/x/y are passed get bounding box
        $zoom = isset($params['zoom']) ? $params['zoom'] : false;
        $x = isset($params['x']) ? $params['x'] : false;
        $y = isset($params['y']) ? $params['y'] : false;
        if ($zoom !== false and
            $x !== false and
            $y !== false) {
            $boundingBox = Tile::tileToBoundingBox($zoom, $x, $y);

            $request->merge(['bbox' => implode(',', $boundingBox->asArray())]);
        }
    }

    // Ushahidi_Rest
    protected function getFilters(Request $request)
    {
        return parent::getFilters($request) + [
            'include_types' => ['point', 'geometry'],
            'output_core_post' => true,
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

        $filters = $this->demoCheck($this->getFilters($request));

        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search')
            ->setFilters($filters)
            ->setIdentifiers($this->getIdentifiers($request))
            ->setFormatter(service('formatter.entity.post.geojsoncollection'));

        return $this->prepResponse($this->executeUsecase($request), $request);
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

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
