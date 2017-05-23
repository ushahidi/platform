<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * FrontlineSms Callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\FrontlineSms
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Sms_Frontlinesms extends Controller {
	protected $_provider = NULL;

	protected $_json = [];

	protected $options = NULL;

	public function action_index()
	{
		// Set up custom error view
		Kohana_Exception::$error_view = 'error/data-provider';

    //Check if data provider is available
    $providers_available = Kohana::$config->load('features.data-providers');

    if ( !$providers_available['frontlinesms'] )
    {
      throw HTTP_Exception::factory(403, 'The Fontline SMS data source is not currently available. It can be accessed by upgrading to a higher Ushahidi tier.');
    }

		$methods_with_http_request = [Http_Request::POST];

		if ( !in_array($this->request->method(),$methods_with_http_request))
		{
			// Only POST or GET is allowed
			throw HTTP_Exception::factory(405, 'The :method method is not supported. Supported methods are :allowed_methods', array(
					':method'          => $this->request->method(),
					':allowed_methods' => implode(',',$methods_with_http_request)
				))
				->allowed($methods_with_http_request);
		}

		$this->_provider = DataProvider::factory('frontlinesms');

		$this->options = $this->_provider->options();

		// Ensure we're always returning a payload..
		// This will be overwritten later if incoming or send methods are run
		$this->_json['payload'] = [
			'success' => TRUE,
			'error' => NULL
		];

		// Process incoming messages from Frontlinecloud only if the request is POST
		if ( $this->request->method() == 'POST')
		{
			$this->_incoming();
		}

		$this->_set_response();
	}

	private function _incoming()
	{
		if ( isset($this->options['secret']) AND $this->request->post('secret') != $this->options['secret'])
		{
			throw new HTTP_Exception_403('Incorrect or missing secret key');
		}

		$from = $this->request->post('from');

		if(empty($from))
		{
			throw new HTTP_Exception_400('Missing from value');
		}

		$message_text = $this->request->post('message');

		if (empty($message_text))
		{
			throw new HTTP_Exception_400('Missing message');
		}

		// Allow for Alphanumeric sender
		$from = preg_replace("/[^0-9A-Za-z ]/", "", $from);

		$options = $this->_provider->options();

		$additional_data = [];
		// Check if a form id is already associated with this data provider
		if (isset($options['form_id'])) {
			$additional_data['form_id'] = $options['form_id'];
			$additional_data['inbound_fields'] = isset($options['inbound_fields']) ? $options['inbound_fields'] : NULL;
		}

		$this->_provider->receive(Message_Type::SMS, $from, $message_text, $additional_data);

		$this->_json['payload'] = [
			'success' => TRUE,
			'error' => NULL
		];
	}

	// Set response message
	private function _set_response()
	{
		$this->response->headers('Content-Type', 'application/json');
		$this->response->body(json_encode($this->_json));
	}
}
