<?php defined('SYSPATH') or die('No direct access allowed.');

class Controller_Sms_Smssync extends Controller {

	public function action_index()
	{
		// Set up custom error view
		Kohana_Exception::$error_view_content_type = 'application/json';
		Kohana_Exception::$error_view = 'error/api';
		Kohana_Exception::$error_layout = FALSE;
		HTTP_Exception_404::$error_view = 'error/api';

		$success = FALSE;
		$messages = array();

		if ($this->request->method() == 'POST')
		{
			$provider = DataProvider::factory('smssync');

			// Authenticate the request
			$options = $provider->options();
			if ($this->request->post('secret') AND
				$this->request->post('secret') == $options['secret'])
			{
				// Remove Non-Numeric characters because that's what the DB has
				$to = preg_replace("/[^0-9,.]/", "", $this->request->post('sent_to'));
				$from = preg_replace("/[^0-9,.]/", "", $this->request->post('from'));
				$message_text = $this->request->post('message');
				$sender = $provider->from();

				// If receiving an SMS Message
				if ($to AND $from AND $message_text)
				{
					$success = TRUE;
					$provider->receive($from, $message_text);
				}

				// Do we have any tasks for SMSSync?
				// Grab messages to send, 20 at a time.
				$messages = $provider->get_outgoing_messages(20);

				$success = TRUE;
			}

			$json = array(
				'payload' => array(
					'success' => $success,
					'messages' => $messages
					)
				);

			// Set the correct content-type header
			$this->response->headers('Content-Type', 'application/json');
			$this->response->body(json_encode($json));
		}
	}
}