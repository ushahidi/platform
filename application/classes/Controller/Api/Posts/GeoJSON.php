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

	/**
	 * @var gisconverter\WKT WKT decoder
	 */
	protected $decoder;

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

		// Geometry Decoder
		$this->decoder = new gisconverter\WKT();
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
			$this->_response_payload['bbox'] = $this->_boundingbox->as_array();
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
		$this->_response_payload = $this->_post_to_feature_collection($post);
	}

	/**
	 * Create a GeoJSON feature from Post JSON
	 *
	 * @param  array   $post          Post JSON Array
	 * @return array
	 */
	protected function _post_to_feature($post)
	{
		// Get possible point attributes
		$point_attributes = $this->_location_attributes();

		// loop over possible locations and add to geometry array
		$geometries = array();
		foreach($point_attributes as $attr)
		{
			// Does the post have this attribute?
			if (array_key_exists($attr->key, $post['values']))
			{
				$geom_key = $attr->key;

				// If only single value
				if (
					! is_array($post['values'][$geom_key]) OR
					// Single point value will have a lat/lon array
					($attr->type == 'point' AND isset($post['values'][$geom_key]['lat']) AND isset($post['values'][$geom_key]['lon']))
				)
				{
					$post['values'][$geom_key] = array(
						array('value' => $post['values'][$geom_key])
					);
				}

				foreach($post['values'][$geom_key] as $value)
				{
					if ($geometry = $this->_value_to_geometry($value['value'], $attr->type))
					{
						$geometries[] = $geometry;
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
				'id' => $post['id'],
				'url' => $post['url'],
				// @todo add mark- attributes based on tag symbol+color
				//'marker-size' => '',
				//'marker-symbol' => '',
				//'marker-color' => '',
				//'resource' => $post
			)
		);
	}

	/**
	 * Create a GeoJSON feature collection from Post JSON
	 *
	 * @param  array   $post          Post JSON Array
	 * @return array
	 */
	protected function _post_to_feature_collection($post)
	{
		// Get possible point attributes
		$point_attributes = $this->_location_attributes();

		// loop over possible locations and add to features array
		$features = array();
		foreach($point_attributes as $attr)
		{
			$geometries = array();

			// Does the post have this attribute?
			if (array_key_exists($attr->key, $post['values']))
			{
				$geom_key = $attr->key;

				// If only single value
				if (
					! is_array($post['values'][$geom_key]) OR
					// Single point value will have a lat/lon array
					($attr->type == 'point' AND isset($post['values'][$geom_key]['lat']) AND isset($post['values'][$geom_key]['lon']))
				)
				{
					$post['values'][$geom_key] = array(
						array(
							'value' => $post['values'][$geom_key],
							'id' => NULL
						)
					);
				}

				foreach($post['values'][$geom_key] as $value)
				{
					if ($geometry = $this->_value_to_geometry($value['value'], $attr->type))
					{
						$features[] = array(
							'type' => 'Feature',
							'geometry' => $geometry,
							'properties' => array(
								'title' => $attr->label,
								'description' => '',
								'attribute_key' => $attr->key,
								'value_id' => $value['id']
								// @todo add mark- attributes based on tag symbol+color
								//'marker-size' => '',
								//'marker-symbol' => '',
								//'marker-color' => '',
								//'resource' => $post
							)
						);
					}
				}
			}
		}

		return array(
			'type' => 'FeatureCollection',
			'features' => $features
		);
	}

	/**
	 * Create a GeoJSON geometry from form field value
	 *
	 * @param  array|string $value    Value
	 * @param  string       $type     Value Type (point or geometry)
	 * @return array
	 */
	protected function _value_to_geometry($value, $type)
	{
		// Point
		if ($type == 'point')
		{
			// ensure values aren't null - in case of junk in DB
			if ($value['lon'] != NULL
				AND $value['lat'] != NULL)
			{
				return array(
					'type' => 'Point',
					'coordinates' => array($value['lon'], $value['lat']),
				);
			}
		}
		// Geometry
		else
		{
			try
			{
				$geometry = $this->decoder->geomFromText($value);
				return $geometry->toGeoArray();
			}
			catch (gisconverter\InvalidText $itex) {
				// Invalid value, just skip it
			}
		}
	}

	protected function _location_attributes()
	{
		if (! $this->_geom_attributes)
		{
			$attr_query = ORM::factory('Form_Attribute')
				->and_where_open()
					->where('type', '=', 'point')
					->or_where('type', '=', 'geometry')
				->and_where_close();

			// If geometry attribute is specified, only get selected attributes
			if ($geom_attr = $this->request->query('geometry_attribute'))
			{
				$geom_attr = is_array($geom_attr) ? $geom_attr : array($geom_attr);
				$attr_query->where('key', 'IN', $geom_attr);
			}

			$this->_geom_attributes = $attr_query->find_all();
		}

		return $this->_geom_attributes;
	}
}