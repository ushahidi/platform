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
	
	protected $server;
	protected $storage;

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
	
	protected function _rebuild_db()
	{
		// determine where the sqlite DB will go
		$dir = DOCROOT.'data/oauth.sqlite';
		
		// remove sqlite file if it exists
		if (file_exists($dir)) {
			unlink($dir);
		}
		
		// rebuild the DB
		$db = new PDO(sprintf('sqlite://%s', $dir));
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec('CREATE TABLE oauth_clients (client_id TEXT, client_secret TEXT, redirect_uri TEXT)');
		$db->exec('CREATE TABLE oauth_access_tokens (access_token TEXT, client_id TEXT, user_id TEXT, expires TIMESTAMP, scope TEXT)');
		$db->exec('CREATE TABLE oauth_authorization_codes (authorization_code TEXT, client_id TEXT, user_id TEXT, redirect_uri TEXT, expires TIMESTAMP, scope TEXT)');
		$db->exec('CREATE TABLE oauth_refresh_tokens (refresh_token TEXT, client_id TEXT, user_id TEXT, expires TIMESTAMP, scope TEXT)');
		
		// add test data
		$db->exec('INSERT INTO oauth_clients (client_id, client_secret) VALUES ("demoapp", "demopass")');
		
		chmod($dir, 0777);
		// $db->exec('INSERT INTO oauth_access_tokens (access_token, client_id) VALUES ("testtoken", "Some Client")');
		// $db->exec('INSERT INTO oauth_authorization_codes (authorization_code, client_id) VALUES ("testcode", "Some Client")');
	}
	
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
		if (!file_exists($sqliteDir = DOCROOT.'data/oauth.sqlite')) {
			// generate sqlite if it does not exist
			$this->_rebuild_db();
		}
		$this->storage = new OAuth2_Storage_Pdo(array('dsn' => 'sqlite:'.$sqliteDir));
		$this->server = new OAuth2_Server($this->storage, array(
				//'token_type'               => 'bearer',
				//'access_lifetime'          => 3600,
				'www_realm'                => 'Ushahidi API',
				//'token_param_name'         => 'access_token',
				//'token_bearer_header_name' => 'Bearer',
				'enforce_state'            => TRUE,
				'allow_implicit'           => TRUE,
			));
		// Add
		$this->server->addGrantType(new OAuth2_GrantType_UserCredentials($this->storage));
		$this->server->addGrantType(new OAuth2_GrantType_AuthorizationCode($this->storage));
		$this->server->addGrantType(new OAuth2_GrantType_ClientCredentials($this->storage));
		$this->server->addGrantType(new OAuth2_GrantType_RefreshToken($this->storage));
		
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
		
		$this->server->setScopeUtil($scopeUtil);
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
		if (! $this->server->validateAuthorizeRequest(OAuth2_Request::createFromGlobals())) {
			 $this->server->getResponse()->send(); exit();
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
		$this->server->handleAuthorizeRequest(OAuth2_Request::createFromGlobals(), $authorized)->send();
		exit();
	}
	
	/**
	 * Token Requests
	 */
	public function action_get_token()
	{
		$this->server->handleTokenRequest(OAuth2_Request::createFromGlobals())->send();
		exit();
	}
	
}
	