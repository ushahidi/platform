<?php

/**
 * Kohana ORM storage for all storage types
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Koauth
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
class Koauth_OAuth2_Storage_ORM implements OAuth2_Storage_AuthorizationCodeInterface,
	OAuth2_Storage_AccessTokenInterface, OAuth2_Storage_ClientCredentialsInterface,
	OAuth2_Storage_UserCredentialsInterface, OAuth2_Storage_RefreshTokenInterface
{
	protected $config;

	public function __construct($config = array())
	{
		$this->config = array_merge(
			array(
				'client_model' => 'OAuth_Client',
				'access_token_model' => 'OAuth_AccessToken',
				'refresh_token_model' => 'OAuth_RefreshToken',
				'code_model' => 'OAuth_AuthorizationCode',
				'user_model' => 'User', // @todo figure this out
			),
			$config
		);
	}

	/* ClientCredentialsInterface */
	public function checkClientCredentials($client_id, $client_secret = NULL)
	{
		$client = ORM::factory($this->config['client_model'])
			->where('client_id', '=', $client_id)
			->find();

		return $client->client_secret == $client_secret;
	}

	public function getClientDetails($client_id)
	{
		$client = ORM::factory($this->config['client_model'])
			->where('client_id', '=', $client_id)
			->find();
		
		return $client->loaded() ? $client->as_array() : FALSE;
	}

	public function checkRestrictedGrantType($client_id, $grant_type)
	{
		$client = $this->getClientDetails($client_id);
		if (isset($client['grant_types']) AND is_array($client['grant_types']))
		{
			return in_array($grant_type, $client['grant_types']);
		}

		// if grant_types are not defined, then none are restricted
		return TRUE;
	}

	/* AccessTokenInterface */
	public function getAccessToken($access_token)
	{
		$token = ORM::factory($this->config['access_token_model'])
			->where('access_token', '=', $access_token)
			->find();

		if ($token->loaded())
		{
			// convert date string back to timestamp
			$token->expires = strtotime($token->expires);
			
			return $token->as_array();
		}

		return FALSE;
	}

	public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = NULL)
	{
		// convert expires to datestring
		$expires = date('Y-m-d H:i:s', $expires);

		$token = ORM::factory($this->config['access_token_model'])
			->where('access_token', '=', $access_token)
			->find();
		
		$token
			->set('access_token', $access_token)
			->set('client_id', $client_id)
			->set('expires', $expires)
			->set('user_id', $user_id)
			->set('scope', $scope)
			->save();
	}

	/* AuthorizationCodeInterface */
	public function getAuthorizationCode($authorization_code)
	{
		$code_model = ORM::factory($this->config['code_model'])
			->where('authorization_code', '=', $authorization_code)
			->find();

		if ($code_model->loaded())
		{
			// convert date string back to timestamp
			$code_model->expires = strtotime($code_model->expires);
			
			return $code_model->as_array();
		}

		return FALSE;
	}

	public function setAuthorizationCode($authorization_code, $client_id, $user_id, $redirect_uri, $expires, $scope = NULL)
	{
		// convert expires to datestring
		$expires = date('Y-m-d H:i:s', $expires);
		
		$code_model = ORM::factory($this->config['code_model'])
			->where('authorization_code', '=', $authorization_code)
			->find();

		$code_model->set('authorization_code', $authorization_code)
			->set('client_id', $client_id)
			->set('user_id', $user_id)
			->set('redirect_uri', $redirect_uri)
			->set('expires', $expires)
			->set('scope', $scope)
			->save();
	}

	public function expireAuthorizationCode($authorization_code)
	{
		DB::delete(ORM::factory($this->config['code_model'])->table_name())
			->where('authorization_code', '=', $authorization_code)
			->execute();
	}

	/* UserCredentialsInterface */
	public function checkUserCredentials($username, $password)
	{
		$auth = Auth::instance();
		// @todo return allowed scopes for user
		return $auth->hash($password) === $auth->password($username);
	}

	public function getUserDetails($username)
	{
		$user = ORM::factory($this->config['user_model']);
		$user
			->where($user->unique_key($username), '=', $username)
			->find();
		
		if ($user->loaded())
		{
			$result = $user->as_array();
			// Have to return 'user_id' to match interface requirements
			$result['user_id'] = $result['id'];
			return $result;
		}
		else
		{
			// Return FALSE here as expected by UserCredentials GrantType
			// despite docs saying return array()
			return FALSE;
		}
	}

	/* RefreshTokenInterface */
	public function getRefreshToken($refresh_token)
	{
		$token = ORM::factory($this->config['refresh_token_model'])
			->where('refresh_token', '=', $refresh_token)
			->find();
		
		if ($token->loaded())
		{
			// convert expires to epoch time
			$token->expires = strtotime($token->expires);
			
			return $token->as_array();
		}

		return FALSE;
	}

	public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = NULL)
	{
		// convert expires to datestring
		$expires = date('Y-m-d H:i:s', $expires);

		ORM::factory($this->config['refresh_token_model'])
			->set('refresh_token', $refresh_token)
			->set('client_id', $client_id)
			->set('user_id', $user_id)
			->set('expires', $expires)
			->set('scope', $scope)
			->save();
	}

	public function unsetRefreshToken($refresh_token)
	{
		$token = DB::delete(ORM::factory($this->config['refresh_token_model'])->table_name())
			->where('refresh_token', '=', $refresh_token)
			->execute();
	}

}
