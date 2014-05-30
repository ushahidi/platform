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

	public function action_index()
	{
		// Set up custom error view
		Kohana_Exception::$error_view_content_type = 'application/json';
		Kohana_Exception::$error_view = 'error/smssync';
		Kohana_Exception::$error_layout = FALSE;
		HTTP_Exception_404::$error_view = 'error/smssync';

		if ($this->request->method() != 'POST')
		{
			// Only POST is allowed
			throw HTTP_Exception::factory(405, 'The :method method is not supported. Supported methods are :allowed_methods', array(
					':method'          => $this->request->method(),
					':allowed_methods' => Http_Request::POST,
				))
				->allowed(Http_Request::POST);
		}

		$provider = DataProvider::factory('smssync');

		// Authenticate the request
		$options = $provider->options();
		if ( !isset($options['secret']) OR (! $this->request->post('secret') OR
			$this->request->post('secret') != $options['secret']))
		{
			throw HTTP_Exception::factory(403, 'Incorrect or missing secret key');
		}

		if ( ! $this->request->post('message'))
		{
			throw HTTP_Exception::factory(403, 'Missing message');
		}

		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $this->request->post('sent_to'));
		$from = preg_replace("/[^0-9,.]/", "", $this->request->post('from'));
		$message_text = $this->request->post('message');

		// If receiving an SMS Message
		if ($to AND $from AND $message_text)
		{
			$provider->receive(Message_Type::SMS, $from, $message_text, $to);
		}

		$json = array(
			'payload' => array(
				'success' => TRUE,
				'error' => NULL
			)
		);

		// Do we have any tasks for SMSSync?
		// Grab messages to send, 20 at a time.
		//
		// We don't know if the SMS from the phone itself work or not,
		// but we'll update the messages status to 'unknown' so that
		// its not picked up again
		$messages = $provider->get_pending_messages(20, Message_Status::PENDING_POLL, Message_Status::UNKNOWN);
		if (count($messages) > 0)
		{
			$json['payload']['task'] = "send";
			$json['payload']['messages'] = $messages;
		}

		// Set the correct content-type header
		$this->response->headers('Content-Type', 'application/json');
		$this->response->body(json_encode($json));
	}
}