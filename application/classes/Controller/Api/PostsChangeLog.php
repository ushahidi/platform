<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_PostsChangeLog extends Ushahidi_Rest {


	protected function _scope()
	{
		return 'postschangelog';
	}

	protected function _resource()
	{
		return 'postschangelog';
	}

	public function action_post_index_collection()
	{
		Kohana::$log->add(Log::INFO, 'Adding a log entry manually...');

		//TODO: QUESTION? should we append the current user here?
		$edited_payload = $this->_payload();

			/* Append user data in the Repo, not here. follow other patterns   ===  $user = $this->getUser();  */
		$user = service('session.user');
		Kohana::$log->add(Log::INFO, 'Current user:'.print_r($user->getId(), true));
		
		$edited_payload['user_id'] = $user->getId();
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'create')
			->setPayload($edited_payload);
	}


	public function action_get_index_collection()
	{

		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'read')
			->setIdentifiers($this->_identifiers());


		/*$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'search')
			//->setFilters($this->_filters())
			->setIdentifiers($this->_identifiers());*/

		parent::action_get_index_collection();
		/*
		// Ensure identifiers are set for parent checks
		 */
	}



	//TODO: REMOVE! overriding this method for debugging purposes...
		/**
		 * Check if access is allowed
		 * Checks if oauth token and user permissions
		 *
		 * @return bool
		 * @throws HTTP_Exception|OAuth_Exception
		 */
		protected function _check_access()
		{
			return; // this is a glorious security override...

			$server = service('oauth.server.resource');

			// Using an "Authorization: Bearer xyz" header is required, except for GET requests
			$require_header = $this->request->method() !== Request::GET;
			$required_scope = $this->_scope();

			try
			{
				$server->isValid($require_header);
				if ($required_scope)
				{
					$server->hasScope($required_scope, true);
				}
			}
			catch (OAuth2Exception $e)
			{
				if (!$this->_is_auth_required() AND
					$e instanceof MissingAccessTokenException)
				{
					// A token is not required, so a missing token is not a critical error.
					return;
				}

				// Auth server returns an indexed array of headers, along with the server
				// status as a header, which must be converted to use with Kohana.
				$raw_headers = $server::getExceptionHttpHeaders($server::getExceptionType($e->getCode()));

				$status = 400;
				$headers = array();
				foreach ($raw_headers as $header)
				{
					if (preg_match('#^HTTP/1.1 (\d{3})#', $header, $matches))
					{
						$status = (int) $matches[1];
					}
					else
					{
						list($name, $value) = explode(': ', $header);
						$headers[$name] = $value;
					}
				}

				$exception = HTTP_Exception::factory($status, $e->getMessage());
				if ($status === 401)
				{
					// Pass through additional WWW-Authenticate headers, but only for
					// HTTP 401 Unauthorized responses!
					$exception->headers($headers);
				}
				throw $exception;
			}
		}


}
