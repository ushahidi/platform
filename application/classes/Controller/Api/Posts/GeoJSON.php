<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Streams Controller
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
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
	protected $record_limit = FALSE;

	/**
	 * @var int Maximum number of results to return
	 */
	protected $record_limit_max = FALSE;
	
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
		}
		
	}
	
	public function action_get_index_collection()
	{
		parent::action_get_index_collection();
		
		$posts = $this->_response_payload['results'];
		$this->_response_payload = array(
			'type' => 'FeatureCollection'
		);
		
		// Add bounding box array if needed
		if ($this->_boundingbox)
		{
			$this->_response_payload['bbox'] = array($this->_boundingbox->west, $this->_boundingbox->north, $this->_boundingbox->east, $this->_boundingbox->south);
		}
		
		$this->_response_payload['features'] = array();
		foreach($posts as $post)
		{
			if ($feature = $this->_post_to_feature($post))
			{
				$this->_response_payload['features'][] = $feature;
			}
		}
	}
	
	public function action_get_index()
	{
		parent::action_get_index();

		$post = $this->_response_payload;
		$this->_response_payload = array(
			'type' => 'FeatureCollection',
			'features' => array()
		);
		
		if ($feature = $this->_post_to_feature($post))
		{
			$this->_response_payload['features'][] = $feature;
		}
	}
	
	protected function _post_to_feature($post)
	{
		// Get possible point attributes
		$point_attributes = $this->location_attributes();
		
		// Geometry Decoder
		$decoder = new gisconverter\WKT();
		
		// loop over possible locations and add to geometry array
		$geometries = array();
		foreach($point_attributes as $attr)
		{
			// Does the post have this attribute?
			if (array_key_exists($attr->key, $post['values']))
			{
				$geom_key = $attr->key;
				// Point
				if ($attr->type == 'point')
				{
					$geometries[] = array(
						'type' => 'Point',
						'coordinates' => array($post['values'][$geom_key]['lon'], $post['values'][$geom_key]['lat']),
					);
				}
				// Geometry
				else
				{
					try
					{
						$geometry = $decoder->geomFromText($post['values'][$geom_key]);
						$geometries[] = $geometry->toGeoJSON();
					}
					catch (gisconverter\InvalidText $itex) {
						// Invalid value, just skip it
					}
				}
			}
		}
		
		// Post doesn't have any geometry values : skip
		if (count($geometries) == 0) 
		{
			return FALSE;
		}
		// Just 1 geometry : return that
		elseif (count($geometries) == 1)
		{
			$geometry = $geometries[0];
		}
		// More than 1 geometry : return geometry collection
		else
		{
			$geometry = array(
				'type' => 'GeometryCollection',
				'geometries' => $geometries
			);
		}
		
		return array(
			'type' => 'Feature',
			'geometry' => $geometry,
			'properties' => array(
				'title' => $post['title'],
				'description' => $post['content'],
				// @todo add mark- attributes based on tag symbol+color
				//'marker-size' => '',
				//'marker-symbol' => '',
				//'marker-color' => '',
				'resource' => $post
			)
		);
	}
	
	protected function location_attributes()
	{
		if (! $this->_geom_attributes)
		{
			$this->_geom_attributes = ORM::factory('Form_Attribute')
				->where('type', '=', 'point')
				->or_where('type', '=', 'geometry')
				->find_all();
		}

		return $this->_geom_attributes;
	}
}