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

		$repo = service('config');
		try
		{
			$results = $repo->all($group);
		}
		catch (InvalidArgumentException $e)
		{
			throw HTTP_Exception::factory(400, $e->getMessage());
		}

		$results = array_map(array($this, '_for_api'), $results);
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

		$repo = service('config');
		try
		{
			$config = $repo->get($group, $key);
		}
		catch (InvalidArgumentException $e)
		{
			throw HTTP_Exception::factory(400, $e->getMessage());
		}

		$this->_response_payload = $this->_for_api($config);
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

		$repo = service('config');
		try
		{
			$config = $repo->set($group, $key, $post['config_value']);
		}
		catch (InvalidArgumentException $e)
		{
			throw HTTP_Exception::factory(400, $e->getMessage());
		}

		$this->_response_payload = $this->_for_api($config);
	}

	protected function _for_api(\Ushahidi\Entity\Config $config)
	{
		return array(
			'group_name' => $config->group,
			'config_key' => $config->key,
			'config_value' => $config->value,
			'allowed_methods' => $this->_allowed_methods()
		);
	}

}
