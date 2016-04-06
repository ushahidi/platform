<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * SMS Sync Callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\SMSSync
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Sms_Smssync extends Controller {

	protected $_provider = NULL;

	protected $_json = [];

	protected $options = NULL;

	public function action_index()
	{
		// Set up custom error view
		Kohana_Exception::$error_view = 'error/data-provider';

    //Check if data provider is available
    $providers_available = Kohana::$config->load('features.data-providers');

    if ( !$providers_available['smssync'] ) 
    {
      throw HTTP_Exception::factory(403, 'The SMS Sync data source is not currently available. It can be accessed by upgrading to a higher Ushahidi tier.');
    }


		$methods_with_http_request = [Http_Request::POST, Http_Request::GET];

		if ( !in_array($this->request->method(),$methods_with_http_request))
		{
			// Only POST or GET is allowed
			throw HTTP_Exception::factory(405, 'The :method method is not supported. Supported methods are :allowed_methods', array(
					':method'          => $this->request->method(),
					':allowed_methods' => implode(',',$methods_with_http_request)
				))
				->allowed($methods_with_http_request);
		}

		$this->_provider = DataProvider::factory('smssync');

		$this->options = $this->_provider->options();

		// Ensure we're always returning a payload..
		// This will be overwritten later if incoming or task methods are run
		$this->_json['payload'] = [
			'success' => TRUE,
			'error' => NULL
		];

		// Process incoming messages from SMSSync only if the request is POST
		if ( $this->request->method() == 'POST')
		{
			$this->_incoming();
		}

		// Attempt Task if request is GET and task type is 'send'
		if ( $this->request->method() == 'GET' AND $this->request->query('task') == 'send')
		{
			$this->_task();
		}

		// Set the response
		$this->_set_response();
	}

	/**
	 * Process messages received from SMSSync
	 */
	private function _incoming()
	{

		if ( isset($this->options['secret']) AND $this->request->post('secret') != $this->options['secret'])
		{
			throw new HTTP_Exception_403('Incorrect or missing secret key');
		}

		$from = $this->request->post('from');

		if( empty($from))
		{
			throw new HTTP_Exception_400('Missing from value');
		}

		$message_text = $this->request->post('message');

		if (empty($message_text))
		{
			throw new HTTP_Exception_400('Missing message');
		}

		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $this->request->post('sent_to'));

		// Allow for Alphanumeric sender
		$from = preg_replace("/[^0-9A-Za-z ]/", "", $from);

		$this->_provider->receive(Message_Type::SMS, $from, $message_text, $to);

		$this->_json['payload'] = [
			'success' => TRUE,
			'error' => NULL
		];
	}

	/**
	 * Implement SMSSync task feature to allow SMSSync to send messages as SMS
	 * to users.
	 */
	private function _task()
	{

		// Validate secret key if set
		if ( isset($this->options['secret']) AND $this->request->query('secret') != $this->options['secret'])
		{
			throw new HTTP_Exception_403('Incorrect or missing secret key');
		}
		// Do we have any tasks for SMSSync?
		// Grab messages to send, 20 at a time.
		//
		// We don't know if the SMS from the phone itself work or not,
		// but we'll update the messages status to 'unknown' so that
		// its not picked up again
		$messages = $this->_provider->get_pending_messages(20, Message_Status::PENDING_POLL, Message_Status::UNKNOWN);

		$this->_json['payload'] = [
			'task' => "send",
			'success' => TRUE,
			'messages' => $messages,
			'secret' => $this->options['secret'] ?: null
		];
	}

	// Set response message
	private function _set_response()
	{
		$this->response->headers('Content-Type', 'application/json');
		$this->response->body(json_encode($this->_json));
	}
}
