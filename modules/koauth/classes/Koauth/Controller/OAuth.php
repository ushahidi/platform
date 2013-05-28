<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * OAuth2 Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Koauth
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
abstract class Koauth_Controller_OAuth extends Controller {
	
	protected $_oauth2_server;
	protected $_skip_oauth_response = FALSE;

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
		$this->_oauth2_server = new Koauth_OAuth2_Server();
	}

	public function after()
	{
		if (! $this->_skip_oauth_response)
		{
			$this->_oauth2_server->processResponse($this->response);
		}
	}
	
	/**
	 * Authorize Requests
	 */
	public function action_get_authorize()
	{
		if (! ($params = $this->_oauth2_server->validateAuthorizeRequest(Koauth_OAuth2_Request::createFromRequest($this->request), new OAuth2_Response())) ) {
			return;
		}

		// Show authorize yes/no
		$this->_skip_oauth_response = TRUE;
		$this->response->body(View::factory('oauth/authorize'));
	}
	
	/**
	 * Authorize Requests
	 */
	public function action_post_authorize()
	{
		$authorized = (bool) $this->request->post('authorize');
		$this->_oauth2_server->handleAuthorizeRequest(Koauth_OAuth2_Request::createFromRequest($this->request), new OAuth2_Response(), $authorized);
	}
	
	/**
	 * Token Requests
	 */
	public function action_get_token()
	{
		$this->_oauth2_server->handleTokenRequest(Koauth_OAuth2_Request::createFromRequest($this->request), new OAuth2_Response());
	}
	
	/**
	 * Token Requests
	 */
	public function action_post_token()
	{
		$this->_oauth2_server->handleTokenRequest(Koauth_OAuth2_Request::createFromRequest($this->request), new OAuth2_Response());
	}
	
}
	