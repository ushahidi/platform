<?php defined('SYSPATH') OR die('No direct access allowed.');

use League\OAuth2\Server\Exception\ClientException as OAuthClientException;

class Controller_OAuth extends Controller_Layout {

	public $template = 'oauth/authorize';

	private $auth;
	private $user;
	private $session;

	private $oauth_params = array(
		'client_id',
		'redirect_uri',
		'response_type',
		'scope',
		'state',
		);

	public function before()
	{
		$action = $this->request->action();
		if ($action AND !in_array($action, array('index', 'authorize')))
		{
			// Only apply templating to index and authorization actions
			$this->auto_render = FALSE;
		}

		parent::before();

		$this->auth    = A1::instance();
		$this->user    = $this->auth->get_user();
		$this->session = $this->auth->session();

		if ($this->auto_render)
		{
			$this->header->set('logged_in', $this->auth->logged_in());
		}
	}

	public function action_index()
	{
		// todo: try/catch OAuthClientException
		$server = service('oauth.server.auth');
		$params = $server->getGrantType('authorization_code')->checkAuthoriseParams();

		$this->session->set('oauth', $params);

		if (!$this->user)
		{
			$this->redirect('user/login' . URL::query(array('from_url' => 'oauth/authorize'. URL::query()), FALSE));
		}

		$this->redirect('oauth/authorize' . URL::query(Arr::extract($params, $this->oauth_params)));
	}

	public function action_authorize()
	{
		if (!$this->user)
		{
			// Not possible to authorize until login is finished, go back to index
			// to restart the flow.
			return $this->action_index();
		}

		$server = service('oauth.server.auth');
		$params = $this->session->get('oauth');

		if ($this->request->post('approve') OR !empty($params['client_details']['auto_approve']))
		{
			// user id has not been injected into the parameters, do it now
			$params['user_id'] = $this->user->id;

			$code = $server->getGrantType('authorization_code')->newAuthoriseRequest('user', $params['user_id'], $params);

			// Redirect the user back to the client with an authorization code
			$this->redirect(
				// todo: this needs to be injected, but it's static. X(
				League\OAuth2\Server\Util\RedirectUri::make($params['redirect_uri'], array(
						'code'  => $code,
						'state' => Arr::get($params, 'state'),
					))
				);
		}

		if ($this->request->post('deny'))
		{
			// Redirect the user back to the client with an error
			$this->redirect(
				// todo: this needs to be injected, but it's static. X(
				League\OAuth2\Server\Util\RedirectUri::make($params['redirect_uri'], array(
					'error'         => 'access_denied',
					'error_message' => $server->getExceptionMessage('access_denied'),
					'state'         => Arr::get($params, 'state'),
					))
				);
		}

		// Load the content template
		$this->template = $view = View::factory('oauth/authorize')
			->set('scopes', Arr::pluck($params['scopes'], 'name'))
			->set('client', $params['client_details']['name'])
			;
	}

	public function action_token()
	{
		$server = service('oauth.server.auth');

		try
		{
			$response = $server->issueAccessToken();
		}
		catch (OAuthClientException $e)
		{
			// Throw an exception because there was a problem with the client's request
			$response = array(
				'error' => $server::getExceptionType($e->getCode()),
				'error_description' => $e->getMessage()
			);
			// Auth server returns an indexed array of headers, along with the server
			// status as a header, which must be converted to use with Kohana.
			$headers = $server::getExceptionHttpHeaders($server::getExceptionType($e->getCode()));
			foreach ($headers as $header)
			{
				if (preg_match('#^HTTP/1.1 (\d{3})#', $header, $matches))
				{
					$this->response->status($matches[1]);
				}
				else
				{
					list($name, $value) = explode(': ', $header);
					$this->response->header($name, $value);
				}
			}
		}
		catch (Exception $e)
		{
			// Throw an error when a non-library specific exception has been thrown
			$response = array(
				'error' =>  'undefined_error',
				'error_description' => $e->getMessage()
			);
			$this->response->status(400);
		}
		$this->response->headers('Content-Type', 'application/json');
		$this->response->body(json_encode($response));
	}
}
