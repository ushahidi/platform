<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * OAuth2 Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_OAuth extends Controller {
	
	protected $_oauth2_server;
	protected $_oauth2_storage;
	protected $_oauth2_response;

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::POST   => 'post',   // Typically Create..
		Http_Request::GET    => 'get',
		Http_Request::PUT    => 'put',    // Typically Update..
		Http_Request::DELETE => 'delete',
	);
	
	public function before()
	{
		parent::before();
		
		// Get the basic verb based action..
		$action = $this->_action_map[$this->request->method()];

		// If this is a custom action, lets make sure we use it.
		if ($this->request->action() != '_none')
		{
			$action .= '_'.$this->request->action();
		}
		
		// Override the action
		$this->request->action($action);

		// Set up OAuth2 objects
		$this->_oauth2_storage = new Kohana_OAuth2_Storage_ORM();
		$this->_oauth2_server = new Oauth2_Server($this->_oauth2_storage, array(
				//'token_type'               => 'bearer',
				//'access_lifetime'          => 3600,
				'www_realm'                => 'Ushahidi API',
				//'token_param_name'         => 'access_token',
				//'token_bearer_header_name' => 'Bearer',
				'enforce_state'            => TRUE,
				'allow_implicit'           => TRUE,
			));
		// Add
		$this->_oauth2_server->addGrantType(new OAuth2_GrantType_UserCredentials($this->_oauth2_storage));
		$this->_oauth2_server->addGrantType(new OAuth2_GrantType_AuthorizationCode($this->_oauth2_storage));
		$this->_oauth2_server->addGrantType(new OAuth2_GrantType_ClientCredentials($this->_oauth2_storage));
		$this->_oauth2_server->addGrantType(new OAuth2_GrantType_RefreshToken($this->_oauth2_storage));
		
		// Configure your available scopes
		$defaultScope = 'basic';
		$supportedScopes = array(
			'basic',
			'posts'
		);
		$memory = new OAuth2_Storage_Memory(array(
			'default_scope' => $defaultScope,
			'supported_scopes' => $supportedScopes
		));
		$scopeUtil = new OAuth2_Scope($memory);
		
		$this->_oauth2_server->setScopeUtil($scopeUtil);
	}

	public function after()
	{
		// Process OAuth2_Response
		if ($this->_oauth2_response instanceof OAuth2_Response)
		{
			$this->response->body($this->_oauth2_response->getResponseBody());
			$this->response->headers($this->_oauth2_response->getHttpHeaders());
			$this->response->headers('Content-Type', 'application/json');
			$this->response->status($this->_oauth2_response->getStatusCode());
		}
	}

	public function action_get_debug()
	{
		echo json_encode($this->request->query());
	}
	
	/**
	 * Authorize Requests
	 */
	public function action_get_authorize()
	{
		if (! $this->_oauth2_server->validateAuthorizeRequest(Kohana_OAuth2_Request::createFromRequest($this->request))) {
			$this->_oauth2_response = $this->_oauth2_server->getResponse();
		}

		// Show authorize yes/no
		
		$this->response->body(View::factory('oauth/authorize'));
	}
	
	/**
	 * Authorize Requests
	 */
	public function action_post_authorize()
	{
		$authorized = (bool) $this->request->post('authorize');
		$this->_oauth2_response = $this->_oauth2_server->handleAuthorizeRequest(Kohana_OAuth2_Request::createFromRequest($this->request), $authorized);
		
	}
	
	/**
	 * Token Requests
	 */
	public function action_get_token()
	{
		$this->_oauth2_response = $this->_oauth2_server->handleTokenRequest(Kohana_OAuth2_Request::createFromRequest($this->request));
	}
	
	/**
	 * Token Requests
	 */
	public function action_post_token()
	{
		$this->_oauth2_response = $this->_oauth2_server->handleTokenRequest(Kohana_OAuth2_Request::createFromRequest($this->request));
	}
	
}
	