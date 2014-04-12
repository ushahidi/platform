<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Config Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Config extends Ushahidi_Api {

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::GET    => 'get',
		Http_Request::PUT    => 'put',    // Typically Update..
	);

	/**
	 * @var string Field to sort results by
	 */
	protected $_record_orderby = 'id';

	/**
	 * @var string Direct to sort results
	 */
	protected $_record_order = 'ASC';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_allowed_orderby = array('id', 'created', 'config_key');

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'config';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'config';
	}

	/**
	 * Retrieve All Config
	 *
	 * GET /api/config
	 *
	 * @return void
	 */
	public function action_get_index_collection()
	{
		$group = $this->request->param('group');
		$groups = Ushahidi_Config_Database::groups();

		if (! empty($group) )
		{
			if (! in_array($group, $groups))
			{
				// no valid group selected
				$groups = array();
			}
			else
			{
				$groups = array($group);
			}
		}

		$results = array();
		foreach($groups as $group)
		{
			$configs = Kohana::$config->load($group)->as_array();
			foreach ($configs as $key => $value)
			{
				$results[] = $this->_for_api($group, $key, $value);
			}
		}

		$count = count($results);

		// Respond with posts
		$this->_response_payload = array(
			'count' => $count,
			'results' => $results
		);
	}

	/**
	 * Retrieve A Config Value
	 *
	 * GET /api/config/:group/:key
	 *
	 * @return void
	 */
	public function action_get_index()
	{
		$group = $this->request->param('group');
		$key = $this->request->param('id');
		$groups = Ushahidi_Config_Database::groups();

		if (! in_array($group, $groups))
		{
			throw HTTP_Exception::factory(400, 'Invalid group');
		}

		$value = Kohana::$config->load($group)->get($key);

		$this->_response_payload = $this->_for_api($group, $key, $value);
	}

	/**
	 * Update A Tag
	 *
	 * PUT /api/config/:group/:key
	 *
	 * @return void
	 */
	public function action_put_index()
	{
		$post = $this->_request_payload;
		$group = $this->request->param('group');
		$key = $this->request->param('id');
		$groups = Ushahidi_Config_Database::groups();

		if (! in_array($group, $groups))
		{
			throw HTTP_Exception::factory(400, 'Invalid group');
		}

		$config = Kohana::$config->load($group)
			->set($key, $post['config_value']);

		$value = $config->get($key);

		$this->_response_payload = $this->_for_api($group, $key, $value);
	}

	protected function _for_api($group, $key, $value)
	{
		return array(
			'group_name' => $group,
			'config_key' => $key,
			'config_value' => $value,
			'allowed_methods' => $this->_allowed_methods()
		);
	}

}
