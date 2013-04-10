<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Base Controller
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
	protected $record_limit = 50;
	
	/**
	 * @var int Offset for results returned
	 */
	protected $record_offset = 0;
	
	/**
	 * @var string Field to sort results by
	 */
	protected $record_orderby = 'id';
	
	/**
	 * @var string Direction to sort results
	 */
	protected $record_order = 'DESC';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $record_limit_max = 500;

	/**
	 * @var int Maximum number of results to return
	 */
	protected $record_allowed_orderby = array('id');
	
	public function before()
	{
		parent::before();

		$this->_parse_request();
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
		if ( ! isset($this->_action_map[$this->request->method()]) )
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
		if ($this->request->param('id', FALSE) === FALSE)
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
	 * @throws Http_Exception_400
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
				
				

				throw new Http_Exception_400('Invalid json supplied. Error: \':error\'. \':json\'', array(
					':json' => $this->request->body(),
					':error' => $error,
				));
			}
			// Ensure JSON object/array was supplied, not string etc
			elseif ( ! is_array($this->_request_payload) AND ! is_object($this->_request_payload) )
			{
				throw new Http_Exception_400('Invalid json supplied. Error: \'JSON must be array or object\'. \':json\'', array(
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
			$this->response->headers('cache-control', 'no-cache, no-store, max-age=0, must-revalidate');
		}

		// Switch based on response format
		$format = strtolower($this->request->query('format'));
		switch($format)
		{
			case 'jsonp':
				// Set the correct content-type header
				$this->response->headers('Content-Type', 'application/javascript');

				$this->_prepare_response_body('jsonp');
				break;
			case 'json':
			default:
				// Set the correct content-type header
				$this->response->headers('Content-Type', 'application/json');

				$this->_prepare_response_body('json');
				break;
		}
	}

	/**
	 * Prepare response body
	 * 
	 * Encode _response_payload into JSON or JSONP
	 *
	 * @todo Add support for GeoJSON
	 * @throws Http_Exception_400|Http_Exception_500
	 */
	protected function _prepare_response_body($format = 'json')
	{
		$body = '';

		try
		{
			// Format the reponse as JSON
			$body = json_encode($this->_response_payload);
		}
		catch (Exception $e)
		{
			throw new Http_Exception_500('Error while formatting response');
		}

		if ($format == 'jsonp')
		{
			$callback = $this->request->query('callback');
			// ensure we have a callback fn
			if (empty($callback))
				throw new Http_Exception_400('Required query parameter \'callback\' is missing or empty.');

			// sanitize callback function name
			if (preg_match("/^[a-zA-Z0-9]+$/", $callback) != 1)
				throw new Http_Exception_400('JSONP callback must be alphanumeric.');

			// wrap body in callback
			$body = "{$callback}({$body})";
		}

		$this->response->body($body);
	}
	
	/**
	 * Prepare request ordering and limit params
	 * @throws Http_Exception_400
	 */
	protected function prepare_order_limit_params()
	{
		$this->record_limit = $this->request->query('limit') ? intval($this->request->query('limit')) : $this->record_limit;
		$this->record_offset = $this->request->query('offset') ? intval($this->request->query('offset')) : $this->record_offset;
		$this->record_orderby = $this->request->query('orderby') ? $this->request->query('orderby') : $this->record_orderby;
		$this->record_order = $this->request->query('order') ? strtoupper($this->request->query('order')) : $this->record_order;

		if (! in_array($this->record_order, array('ASC', 'DESC')))
			throw new Http_Exception_400('Invalid \'order\' parameter supplied: :order.', array(
				':order' => $this->record_order
			));

		if (! in_array($this->record_orderby, $this->record_allowed_orderby))
			throw new Http_Exception_400('Invalid \'orderby\' parameter supplied: :orderby.', array(
				':orderby' => $this->record_orderby
			));

		if ($this->record_limit > $this->record_limit_max)
			throw new Http_Exception_400('Number of records requested was too large: :record_limit.', array(
				':record_limit' => $this->record_limit
			));
	}
}
