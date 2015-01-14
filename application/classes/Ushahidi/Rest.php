<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi REST Base Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Api\Endpoint;
use League\OAuth2\Server\Exception\OAuth2Exception;
use League\OAuth2\Server\Exception\MissingAccessTokenException;

abstract class Ushahidi_Rest extends Controller {
	use Ushahidi_Corsheaders;

	/**
	 * @var Current API version
	 */
	protected static $version = '2';

	/**
	 * @var Object Request Payload
	 */
	protected $_request_payload = [];

	/**
	 * @var Object Response Payload
	 */
	protected $_response_payload = NULL;

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::POST    => 'post',   // Typically Create..
		Http_Request::GET     => 'get',
		Http_Request::PUT     => 'put',    // Typically Update..
		Http_Request::DELETE  => 'delete',
		Http_Request::OPTIONS => 'options'
	);

	/**
	 * @var array List of HTTP methods which support body content
	 */
	protected $_methods_with_body_content = array
	(
		Http_Request::POST,
		Http_Request::PUT,
	);

	/**
	 * @var array List of HTTP methods which may be cached
	 */
	protected $_cacheable_methods = array
	(
		Http_Request::GET,
	);

	/**
	 * Get the required scope for this endpoint.
	 * @return string
	 */
	abstract protected function _scope();

	public function before()
	{
		parent::before();

		$this->_parse_request();
		$this->_check_access();
	}

	public function after()
	{
		$this->_prepare_response();

		parent::after();
	}

	/**
	 * Get current api version
	 */
	public static function version()
	{
		return self::$version;
	}

	/**
	 * Get an API URL for a resource.
	 * @param  string  $resource
	 * @param  mixed   $id
	 * @return string
	 */
	public static function url($resource, $id = null)
	{
		$template = 'api/v%d/%s';
		if (!is_null($id)) {
			$template .= '/%s';
		}
		return rtrim(sprintf($template, static::version(), $resource, $id), '/');
	}

	/**
	 * Get options for a resource collection.
	 *
	 * OPTIONS /api/foo
	 *
	 * @return void
	 */
	public function action_options_index_collection()
	{
		$this->response->status(200);
	}

	/**
	 * Get options for a resource.
	 *
	 * OPTIONS /api/foo
	 *
	 * @return void
	 */
	public function action_options_index()
	{
		$this->response->status(200);
	}

	/**
	 * Get the request access method
	 *
	 * Allows controllers to customize how different methods are treated.
	 *
	 * @return string
	 */
	protected function _get_access_method()
	{
		return strtolower($this->request->method());
	}

	/**
	 * Determines if this request can skip the authorization check.
	 *
	 * @return bool
	 */
	protected function _is_auth_required()
	{
		// Auth is not required for the OPTIONS method, because headers are
		// not present in OPTIONS requests. ;)
		return ($this->request->method() !== Request::OPTIONS);
	}

	/**
	 * Check if access is allowed
	 * Checks if oauth token and user permissions
	 *
	 * @return bool
	 * @throws HTTP_Exception|OAuth_Exception
	 */
	protected function _check_access()
	{
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
			if (!$this->_is_auth_required() AND $e instanceof MissingAccessTokenException)
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

	/**
	 * Parse the request...
	 */
	protected function _parse_request()
	{
		// Override the method if needed.
		$this->request->method(Arr::get(
			$_SERVER,
			'HTTP_X_HTTP_METHOD_OVERRIDE',
			$this->request->method()
		));

		// Is that a valid method?
		if ( ! isset($this->_action_map[$this->request->method()]))
		{
			throw HTTP_Exception::factory(405, 'The :method method is not supported. Supported methods are :allowed_methods', array(
				':method'          => $this->request->method(),
				':allowed_methods' => implode(', ', array_keys($this->_action_map)),
			))
			->allowed(array_keys($this->_action_map));
		}

		// Get the basic verb based action..
		$action = $this->_action_map[$this->request->method()];

		// If this is a custom action, lets make sure we use it.
		if ($this->request->action() != '_none')
		{
			$action .= '_'.$this->request->action();
		}

		// If we are acting on a collection, append _collection to the action name.
		if ($this->request->param('id', FALSE) === FALSE
			AND $this->request->param('locale', FALSE) === FALSE)
		{
			$action .= '_collection';
		}

		// Override the action
		$this->request->action($action);

		if (! method_exists($this, 'action_'.$action))
		{
			// TODO: filter 'Allow' header to only return implemented methods
			throw HTTP_Exception::factory(405, 'The :method method is not supported. Supported methods are :allowed_methods', array(
				':method'          => $this->request->method(),
				':allowed_methods' => implode(', ', array_keys($this->_action_map)),
			))
			->allowed(array_keys($this->_action_map));
		}

		// Are we be expecting body content as part of the request?
		if (in_array($this->request->method(), $this->_methods_with_body_content))
		{
			$this->_parse_request_body();
		}
	}

	protected $json_errors = [
		JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
		JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
		JSON_ERROR_CTRL_CHAR      => 'Unexpected control character found',
		JSON_ERROR_SYNTAX         => 'Syntax error, malformed JSON',
		JSON_ERROR_UTF8           => 'Malformed UTF-8 characters, possibly incorrectly encoded',
	];

	/**
	 * If the request has a JSON body, parse it into native type.
	 *
	 * @todo Support more than just JSON
	 * @throws HTTP_Exception_400
	 */
	protected function _parse_request_body()
	{
			$payload = json_decode($this->request->body(), true);

			// Ensure there were no JSON errors
			$error = json_last_error();
			if ($error AND $error !== JSON_ERROR_NONE)
			{
				throw new HTTP_Exception_400('Invalid json supplied. Error: \':error\'. \':json\'', array(
					':json' => $this->request->body(),
					':error' => Arr::get($this->json_errors, $error, 'Unknown error'),
				));
			}

			// Ensure JSON object/array was supplied, not string etc
			if ( ! is_array($payload) AND ! is_object($payload) )
			{
				throw new HTTP_Exception_400('Invalid json supplied. Error: \'JSON must be array or object\'. \':json\'', array(
					':json' => $this->request->body(),
				));
			}

			$this->_request_payload = $payload;
	}

	/**
	 * Prepare response headers and body, formatted based on user request.
	 * @throws HTTP_Exception_400
	 * @throws HTTP_Exception_500
	 * @return void
	 */
	protected function _prepare_response()
	{
		$this->add_cors_headers($this->response);

		// Should we prevent this request from being cached?
		if ( ! in_array($this->request->method(), $this->_cacheable_methods))
		{
			$this->response->headers('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
		}

		// Get the requested response format, use JSON for default
		$type = strtolower($this->request->query('format')) ?: 'json';

		try
		{
			$format = service("formatter.output.$type");

			$body = $format($this->_response_payload);
			$mime = $format->getMimeType();

			if ($type === 'jsonp')
			{
				// Prevent Opera and Chrome from executing the response as anything
				// other than JSONP, see T455.
				$this->response->headers('X-Content-Type-Options', 'nosniff');
			}

			$this->response->headers('Content-Type', $mime);
			$this->response->body($body);
		}
		catch (Aura\Di\Exception\ServiceNotFound $e)
		{
			throw new HTTP_Exception_400(
				'Unknown response format: :format',
				[':format' => $type]
			);
		}
		catch (InvalidArgumentException $e)
		{
			throw new HTTP_Exception_400(
				'Bad formatting parameters: :message',
				[':message' => $e->getMessage()]
			);
		}
		catch (Ushahidi\Core\Exception\FormatterException $e)
		{
			throw new HTTP_Exception_500(
				'Error while formatting response: :message',
				[':message' => $e->getMessage()]
			);
		}
	}

	/**
	 * Run an Endpoint request sequence and convert application exceptions into
	 * Kohana HTTP exceptions.
	 * @throws HTTP_Exception_400
	 * @throws HTTP_Exception_403
	 * @throws HTTP_Exception_404
	 * @param  Ushahidi\Endpoint $endpoint
	 * @param  Array $request
	 * @return void
	 */
	protected function _restful(Endpoint $endpoint, Array $request)
	{
		try
		{
			$this->_response_payload = $endpoint->run($request);
		}
		catch (Ushahidi\Core\Exception\NotFoundException $e)
		{
			throw new HTTP_Exception_404($e->getMessage());
		}
		catch (Ushahidi\Core\Exception\AuthorizerException $e)
		{
			throw new HTTP_Exception_403($e->getMessage());
		}
		catch (Ushahidi\Core\Exception\ValidatorException $e)
		{
			// Also handles ParserException
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
	}
}
