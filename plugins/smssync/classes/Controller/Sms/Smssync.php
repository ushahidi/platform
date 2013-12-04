<?php defined('SYSPATH') or die('No direct access allowed.');

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
		if (! $this->request->post('secret') OR
			$this->request->post('secret') != $options['secret'])
		{
			throw HTTP_Exception::factory(403, 'Incorrect or missing secret key');
		}

		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $this->request->post('sent_to'));
		$from = preg_replace("/[^0-9,.]/", "", $this->request->post('from'));
		$message_text = $this->request->post('message');
		$sender = $provider->from();

		// If receiving an SMS Message
		if ($to AND $from AND $message_text)
		{
			$provider->receive($from, $message_text);
		}

		$json = array(
			'payload' => array(
				'success' => TRUE,
				'error' => NULL
			)
		);

		// Do we have any tasks for SMSSync?
		// Grab messages to send, 20 at a time.
		$messages = $provider->get_outgoing_messages(20);

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