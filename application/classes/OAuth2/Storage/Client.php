<?php defined('SYSPATH') or die('No direct script access');
/**
 * OAuth2 Storage for Sessions
 *
 * License is MIT, to be more compatible with PHP League.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\OAuth2
 * @copyright  2014 Ushahidi
 * @license    http://mit-license.org/
 * @link       http://github.com/php-loep/oauth2-server
 */

use League\OAuth2\Server\Storage\ClientInterface;

class OAuth2_Storage_Client extends OAuth2_Storage implements ClientInterface
{
	/**
	 * Validate a client
	 *
	 * Example SQL query:
	 *
	 * <code>
	 * # Client ID + redirect URI
	 * SELECT oauth_clients.id, oauth_clients.secret, oauth_client_endpoints.redirect_uri, oauth_clients.name,
	 * oauth_clients.auto_approve
	 *  FROM oauth_clients LEFT JOIN oauth_client_endpoints ON oauth_client_endpoints.client_id = oauth_clients.id
	 *  WHERE oauth_clients.id = :clientId AND oauth_client_endpoints.redirect_uri = :redirectUri
	 *
	 * # Client ID + client secret
	 * SELECT oauth_clients.id, oauth_clients.secret, oauth_clients.name, oauth_clients.auto_approve FROM oauth_clients 
	 * WHERE oauth_clients.id = :clientId AND oauth_clients.secret = :clientSecret
	 *
	 * # Client ID + client secret + redirect URI
	 * SELECT oauth_clients.id, oauth_clients.secret, oauth_client_endpoints.redirect_uri, oauth_clients.name,
	 * oauth_clients.auto_approve FROM oauth_clients LEFT JOIN oauth_client_endpoints 
	 * ON oauth_client_endpoints.client_id = oauth_clients.id
	 * WHERE oauth_clients.id = :clientId AND oauth_clients.secret = :clientSecret AND
	 * oauth_client_endpoints.redirect_uri = :redirectUri
	 * </code>
	 *
	 * Response:
	 *
	 * <code>
	 * Array
	 * (
	 *     [client_id] => (string) The client ID
	 *     [client secret] => (string) The client secret
	 *     [redirect_uri] => (string) The redirect URI used in this request
	 *     [name] => (string) The name of the client
	 *     [auto_approve] => (bool) Whether the client should auto approve
	 * )
	 * </code>
	 *
	 * @param  string     $clientId     The client's ID
	 * @param  string     $clientSecret The client's secret (default = "null")
	 * @param  string     $redirectUri  The client's redirect URI (default = "null")
	 * @param  string     $grantType    The grant type used in the request (default = "null")
	 * @return bool|array               Returns false if the validation fails, array on success
	 */
	public function getClient($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
	{
		// NOTE: this implementation does not implement any grant type checks!

		if (!$clientSecret AND !$redirectUri)
			return FALSE;

		if ($redirectUri AND $clientId === $this->get_internal_client_id())
		{
			// The internal client only supports local redirects, so we strip the
			// domain information from the URI. This also prevents accidental redirect
			// outside of the current domain.
			$redirectUri = parse_url($redirectUri, PHP_URL_PATH);

			// We attempt to strip out the base URL, so that deployments work properly
			// when installed to a sub-directory.
			$baseUrl = preg_quote(URL::base(NULL, true), '~');
			$redirectUri = preg_replace("~^{$baseUrl}~", '/', $redirectUri);
		}

		if ($clientSecret AND $redirectUri)
		{
			$query = $this->query_secret_and_redirect_uri($clientId, $clientSecret, $redirectUri);
		}
		else if ($clientSecret)
		{
			$query = $this->query_secret($clientId, $clientSecret);
		}
		else if ($redirectUri)
		{
			$query = $this->query_redirect_uri($clientId, $redirectUri);
		}

		$query
			->param(':clientId', $clientId)
			->param(':clientSecret', $clientSecret)
			->param(':redirectUri', $redirectUri);

		return $this->select_one_result($query);
	}

	private function get_internal_client_id()
	{
		return Kohana::$config->load('ushahidiui.oauth.client');
	}

	private function query_secret_and_redirect_uri()
	{
		return DB::query(Database::SELECT, '
		SELECT oauth_clients.id, oauth_clients.secret, oauth_client_endpoints.redirect_uri, oauth_clients.name, oauth_clients.auto_approve
		  FROM oauth_clients
		  LEFT JOIN oauth_client_endpoints
		    ON oauth_client_endpoints.client_id = oauth_clients.id
		 WHERE oauth_clients.id = :clientId
		   AND oauth_clients.secret = :clientSecret
		   AND oauth_client_endpoints.redirect_uri = :redirectUri');
	}

	private function query_secret()
	{
		return DB::query(Database::SELECT, '
		SELECT oauth_clients.id, oauth_clients.secret, "" AS redirect_uri, oauth_clients.name, oauth_clients.auto_approve
		  FROM oauth_clients
		 WHERE oauth_clients.id = :clientId
		   AND oauth_clients.secret = :clientSecret');
	}

	private function query_redirect_uri()
	{
		return DB::query(Database::SELECT, '
		SELECT oauth_clients.id, oauth_clients.secret, oauth_client_endpoints.redirect_uri, oauth_clients.name, oauth_clients.auto_approve
		  FROM oauth_clients
		  LEFT JOIN oauth_client_endpoints
		    ON oauth_client_endpoints.client_id = oauth_clients.id
		 WHERE oauth_clients.id = :clientId
		   AND oauth_client_endpoints.redirect_uri = :redirectUri');
	}
}
