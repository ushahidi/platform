<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_OAuth2_Server extends OAuth2_Server
{
	public function __construct($storage = array(), array $config = array(), array $grantTypes = array(), array $responseTypes = array(), OAuth2_ResponseType_AccessTokenInterface $accessTokenResponseType = null, OAuth2_ScopeInterface $scopeUtil = null)
	{
		if (empty($storage))
		{
			$storage = new Kohana_OAuth2_Storage_ORM();
		}

		if (empty($config))
		{
			$config = array(
				//'token_type'               => 'bearer',
				//'access_lifetime'          => 3600,
				'www_realm'                => 'Ushahidi API',
				//'token_param_name'         => 'access_token',
				//'token_bearer_header_name' => 'Bearer',
				'enforce_state'            => TRUE,
				'allow_implicit'           => TRUE,
			);
		}
		
		if (empty($grantTypes))
		{
			// Add grant types
			$grantTypes = array(
				new OAuth2_GrantType_UserCredentials($storage),
				new OAuth2_GrantType_AuthorizationCode($storage),
				new OAuth2_GrantType_ClientCredentials($storage),
				new OAuth2_GrantType_RefreshToken($storage)
			);
		}
		
		if ($scopeUtil == NULL)
		{
			// Configure your available scopes
			$defaultScope = 'api';
			$supportedScopes = array(
				'api',
				'posts',
				'forms'
			);
			$memory = new OAuth2_Storage_Memory(array(
				'default_scope' => $defaultScope,
				'supported_scopes' => $supportedScopes
			));
			$scopeUtil = new OAuth2_Scope($memory);
		}
		
		parent::__construct($storage, $config, $grantTypes, $responseTypes, $accessTokenResponseType, $scopeUtil);
	}
	
	public function processResponse(Kohana_Response &$koresponse)
	{
		if ($this->response instanceof OAuth2_Response_Error)
		{
			$exception = HTTP_Exception::factory(
				$this->response->getStatusCode(),
				$this->response->getError() .": ". $this->response->getErrorDescription()
			);
			// If this is a 401 - copy the WWW-Authenticate header too
			if ($this->response->getStatusCode() == 401)
			{
				$headers = $this->response->getHttpHeaders();
				$exception->authenticate($headers['WWW-Authenticate']);
			}
			
			throw $exception;
		}
		// Handle normal response
		elseif ($this->response instanceof OAuth2_Response)
		{
			$koresponse->body($this->response->getResponseBody());
			$koresponse->headers($this->response->getHttpHeaders());
			$koresponse->headers('Content-Type', 'application/json');
			$koresponse->status($this->response->getStatusCode());
		}
	}
}
