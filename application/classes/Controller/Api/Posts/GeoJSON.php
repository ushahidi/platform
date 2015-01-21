<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Streams Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Posts_GeoJSON extends Controller_Api_Posts {

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::GET    => 'get',
	);

	/**
	 * @var int Number of results to return
	 */
	protected $_record_limit = FALSE;

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_limit_max = FALSE;

	/**
	 * @var Database_Result Collection of all Point/Geometry Attributes
	 */
	protected $_geom_attributes = FALSE;

	public function before()
	{
		parent::before();

		// If zoom/x/y are passwed get bounding box
		$zoom = $this->request->param('zoom', FALSE);
		$x = $this->request->param('x', FALSE);
		$y = $this->request->param('y', FALSE);
		if ($zoom !== FALSE AND
			$x !== FALSE AND
			$y !== FALSE)
		{
			$this->_boundingbox = Util_Tile::tileToBoundingBox($zoom, $x, $y);

			$this->request->query('bbox', implode(',', $this->_boundingbox->as_array()));
		}
	}


	/**
	 * Retrieve All Posts
	 *
	 * GET /api/posts
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$extra_params = [
			'type'   => $this->_type,
			'parent' => $this->request->param('parent_id', NULL),
			'include_types' => ['point', 'geojson']
		];

		$usecase = service('factory.usecase')->get('posts', 'search')
			->setFilters($extra_params + $this->request->query())
			->setFormatter(service('formatter.entity.post.geojsoncollection'));

		$this->_restful($usecase);
	}

	/**
	 * Retrieve A Post
	 *
	 * GET /api/posts/:id
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$usecase = service('factory.usecase')->get('posts', 'read')
			->setIdentifiers($this->request->param())
			->setFormatter(service('formatter.entity.post.geojson'));

		$this->_restful($usecase);
	}
}
