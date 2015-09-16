<?php defined('SYSPATH') OR die('No direct access allowed.');

use League\OAuth2\Server\Exception\ClientException as OAuthClientException;

class Controller_OAuth extends Controller {
	use Ushahidi_Corsheaders;

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
		if ($this->request->method() == HTTP_Request::OPTIONS)
		{
			$this->request->action('options');
		}

		parent::before();
	}

	public function after()
	{
		$this->add_cors_headers($this->response);

		parent::after();
	}

	public function action_options()
	{
		$this->response->status(200);
	}

	/* public function action_index()
	{
		$this->response->status(200);
		// todo: try/catch OAuthClientException
		$server = service('oauth.server.auth');
		$params = $server->getGrantType('authorization_code')->checkAuthoriseParams();

		$this->session->set('oauth', $params);

		if (!$this->user)
		{
			$this->redirect('user/login' . URL::query(array('from_url' => 'oauth/authorize'. URL::query()), FALSE));
		}

		$this->redirect('oauth/authorize' . URL::query(Arr::extract($params, $this->oauth_params)));
	}*/

	public function action_token()
	{
		$server = service('oauth.server.auth');

		try
		{
			$response = $server->issueAccessToken(json_decode($this->request->body(), TRUE));
			if (!empty($response['refresh_token'])) {
				$response['refresh_token_expires_in'] = $server->getGrantType('refresh_token')->getRefreshTokenTTL();
			}
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
