<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Base Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Api extends Controller {

	/**
	 * @var Current API version
	 */
	protected static $version = '2';

	/**
	 * @var Object Request Payload
	 */
	protected $_request_payload = NULL;

	/**
	 * @var Object Response Payload
	 */
	protected $_response_payload = NULL;

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
	 * @var int Number of results to return
	 */
	protected $_record_limit = 50;

	/**
	 * @var int Offset for results returned
	 */
	protected $_record_offset = 0;

	/**
	 * @var string Field to sort results by
	 */
	protected $_record_orderby = 'id';

	/**
	 * @var string Direction to sort results
	 */
	protected $_record_order = 'DESC';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_limit_max = 500;

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_allowed_orderby = array('id');

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'undefined';

	/**
	 * @var string|ORM  resource used for access check and get/:id put/:id requests
	 */
	protected $_resource;

	protected $_auth;
	protected $_acl;
	protected $_user;

	public function before()
	{
		parent::before();

		// Set up custom error view
		Kohana_Exception::$error_view_content_type = 'application/json';
		Kohana_Exception::$error_view = 'error/api';
		Kohana_Exception::$error_layout = FALSE;
		HTTP_Exception_404::$error_view = 'error/api';

		$this->acl  = service('acl');
		$this->auth = $this->acl->auth();

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
		return rtrim(sprintf('api/v%d/%s/%d', static::version(), $resource, $id), '/');
	}

	/**
	 * Get resource object/string
	 * @return string|ORM
	 */
	public function resource()
	{
		if ( ! isset($this->_resource))
		{
			// Initialize the validation object
			$this->_resource();
		}

		return $this->_resource;
	}

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		// @todo split this up by get resource for collection, and get individual resource.. or maybe by action?
		$this->_resource = 'undefined';
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

		try
		{
			$server->isValid($require_header);
		}
		catch (League\OAuth2\Server\Exception\OAuth2Exception $e)
		{

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

		$this->user = ORM::factory('User', $server->getOwnerId());
		$resource   = $this->resource();
		$method     = $this->_get_access_method();

		// Does the user have required role/permissions ?
		if (! $this->acl->is_allowed($this->user, $resource, $method) )
		{
			// @todo proper message
			if (isset($resource->id))
				throw HTTP_Exception::factory('403', 'You do not have permission to access :resource id :id', array(
					':resource' => $resource instanceof Acl_Resource_Interface ? $resource->get_resource_id() : $resource,
					':id' => $resource->id
					));
			else
			{
				throw HTTP_Exception::factory('403', 'You do not have permission to access :resource', array(
					':resource' => $resource instanceof Acl_Resource_Interface ? $resource->get_resource_id() : $resource,
					));
			}
			return FALSE;
		}

		return TRUE;
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
		if ($this->request->param('id', FALSE) === FALSE AND
			$this->request->param('locale', FALSE) === FALSE)
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

	/**
	 * Parse the request body
	 * Decodes JSON request body into PHP array
	 *
	 * @todo Support more than just JSON
	 * @throws HTTP_Exception_400
	 */
	protected function _parse_request_body()
	{
			$this->_request_payload = json_decode($this->request->body(), TRUE);

			if ( $this->_request_payload === NULL )
			{
				// Get further error info
				switch (json_last_error()) {
					case JSON_ERROR_NONE:
						$error = 'No errors';
					break;
					case JSON_ERROR_DEPTH:
						$error = 'Maximum stack depth exceeded';
					break;
					case JSON_ERROR_STATE_MISMATCH:
						$error = 'Underflow or the modes mismatch';
					break;
					case JSON_ERROR_CTRL_CHAR:
						$error = 'Unexpected control character found';
					break;
					case JSON_ERROR_SYNTAX:
						$error = 'Syntax error, malformed JSON';
					break;
					case JSON_ERROR_UTF8:
						$error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
					break;
					default:
						$error = 'Unknown error';
					break;
				}



				throw new HTTP_Exception_400('Invalid json supplied. Error: \':error\'. \':json\'', array(
					':json' => $this->request->body(),
					':error' => $error,
				));
			}
			// Ensure JSON object/array was supplied, not string etc
			elseif ( ! is_array($this->_request_payload) AND ! is_object($this->_request_payload) )
			{
				throw new HTTP_Exception_400('Invalid json supplied. Error: \'JSON must be array or object\'. \':json\'', array(
					':json' => $this->request->body(),
				));
			}
	}

	/**
	 * Prepare response headers and body
	 */
	protected function _prepare_response()
	{
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
			throw new HTTP_Exception_400('Unknown response format: :format', array(':format' => $type));
		}
		catch (InvalidArgumentException $e)
		{
			throw new HTTP_Exception_400('Bad formatting parameters: :message', array(':message' => $e->getMessage()));
		}
		catch (Ushahidi\Exception\FormatterException $e)
		{
			throw new HTTP_Exception_500('Error while formatting response: :message', array(':message' => $e->getMessage()));
		}
	}

	/**
	 * Prepare request ordering and limit params
	 * @throws HTTP_Exception_400
	 */
	protected function _prepare_order_limit_params()
	{
		$this->_record_limit = $this->request->query('limit') ? intval($this->request->query('limit')) : $this->_record_limit;
		$this->_record_offset = $this->request->query('offset') ? intval($this->request->query('offset')) : $this->_record_offset;
		$this->_record_orderby = $this->request->query('orderby') ? $this->request->query('orderby') : $this->_record_orderby;
		$this->_record_order = $this->request->query('order') ? strtoupper($this->request->query('order')) : $this->_record_order;

		if (! in_array($this->_record_order, array('ASC', 'DESC')))
			throw new HTTP_Exception_400('Invalid \'order\' parameter supplied: :order.', array(
				':order' => $this->_record_order
			));

		if (! in_array($this->_record_orderby, $this->_record_allowed_orderby))
			throw new HTTP_Exception_400('Invalid \'orderby\' parameter supplied: :orderby.', array(
				':orderby' => $this->_record_orderby
			));

		if ($this->_record_limit_max !== FALSE AND $this->_record_limit > $this->_record_limit_max)
			throw new HTTP_Exception_400('Number of records requested was too large: :record_limit.', array(
				':record_limit' => $this->_record_limit
			));

		// Replace a negative offset with 0
		if ($this->_record_offset !== FALSE AND $this->_record_offset < 0)
		{
			$this->_record_offset = 0;
		}
	}

	/**
	 * Get the paging parameters for the current collection request.
	 * @return Array  limit, offset, order, orderby, curr, next, prev
	 */
	protected function _get_paging_parameters()
	{
		$params = array(
			'limit' => $this->_record_limit,
			'offset' => $this->_record_offset,
		);

		// Only add order/orderby if they're already set
		if ($this->request->query('orderby') OR $this->request->query('order'))
		{
			$params['orderby'] = $this->_record_orderby;
			$params['order'] = $this->_record_order;
		}

		$prev_params = $next_params = $params;
		$next_params['offset'] = $params['offset'] + $params['limit'];
		$prev_params['offset'] = $params['offset'] - $params['limit'];
		$prev_params['offset'] = $prev_params['offset'] > 0 ? $prev_params['offset'] : 0;

		$curr = URL::site($this->request->uri() . URL::query($params),      $this->request);
		$next = URL::site($this->request->uri() . URL::query($next_params), $this->request);
		$prev = URL::site($this->request->uri() . URL::query($prev_params), $this->request);

		return array(
			'limit'   => $this->_record_limit,
			'offset'  => $this->_record_offset,
			'order'   => $this->_record_order,
			'orderby' => $this->_record_orderby,
			'curr'    => $curr,
			'next'    => $next,
			'prev'    => $prev,
		);
	}

	/**
	 * Get allowed HTTP method for current resource
	 * @param  boolean $resource Optional resources to check access for
	 * @return Array             Array of methods, TRUE if allowed
	 */
	protected function _allowed_methods($resource = FALSE)
	{
		if (! $resource)
		{
			$resource = $this->resource();
		}

		return array(
					'get' => $this->acl->is_allowed($this->user, $resource, 'get'),
					'post' => $this->acl->is_allowed($this->user, $resource, 'post'),
					'put' => $this->acl->is_allowed($this->user, $resource, 'put'),
					'delete' => $this->acl->is_allowed($this->user, $resource, 'delete')
				);
	}
}
