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
			$this->_response_payload['features'][] = $this->_post_to_feature($post);
		}
	}
	
	public function action_get_index()
	{
		parent::action_get_index();

		$post = $this->_response_payload;
		$this->_response_payload = array(
			'type' => 'FeatureCollection',
			'features' => array(
				$this->_post_to_feature($post),
			)
		);
	}
	
	protected function _post_to_feature($post)
	{
		// get possible point attributes
		// loop over possible locations and add to geometry array
		// If geometry count == 0 : skip
		// If geometry count == 1 : just return that
		// If geometry count > 1  : convert to geometry collection
		
		return array(
			'type' => 'Feature',
			'geometry' => array(
				'type' => 'Point',
				'coordinates' => array($post['values']['location']['lon'], $post['values']['location']['lat']),
			),
			'properties' => array(
				'title' => $post['title'],
				'description' => $post['content'],
				//'marker-size' => '',
				//'marker-symbol' => '',
				//'marker-color' => '',
				'resource' => $post
			)
		);
	}
}