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

	public function verify_google_2fa($user, $user_repo, $request_payload)
	{
		// Verify google2fa secret
		$google2fa_otp = $request_payload['google2fa_otp'];
		$valid = $user_repo->verifyGoogle2fa($user, $google2fa_otp);
		return $valid;
	}

	public function action_token()
	{
		$server = service('oauth.server.auth');
		try
		{
			$user2fa_validated = true;
			$request_payload = json_decode($this->request->body(), TRUE);
			if ($request_payload && array_key_exists('username', $request_payload))
			{
				$user_repo = service('repository.user');
				$user = $request_payload['username'] ? $user_repo->getByEmail($request_payload['username']) : array();
				// Check if User has enabled 2fa
				if ($user->google2fa_enabled)
				{
					// Check if Google 2fa secret was provided in payload
					if (!array_key_exists('google2fa_otp', $request_payload))
					{
						$response = array(
						'error' => 'google2fa_secret_required',
						'error_description' => 'Google 2fa secret not provided'
						);
						$this->response->status(401);
						$user2fa_validated = false;
					}
					// Check if the Google 2fa secret is valid
					elseif (!$this->verify_google_2fa($user, $user_repo, $request_payload))
					{
						$response = array(
							'error' => 'google2fa_secret_invalid',
							'error_description' => 'Google 2fa secret not invalid'
						);
						$this->response->status(401);
						$user2fa_validated = false;
					}
				}
			}
			if ($user2fa_validated)
			{
				$response = $server->issueAccessToken($request_payload);
				if (!empty($response['refresh_token']))
				{
					$response['refresh_token_expires_in'] = $server->getGrantType('refresh_token')->getRefreshTokenTTL();
				}
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
				'error' =>	'undefined_error',
				'error_description' => $e->getMessage()
			);
			$this->response->status(400);
		}

		$this->response->headers('Content-Type', 'application/json');
		$this->response->body(json_encode($response));
	}
}
