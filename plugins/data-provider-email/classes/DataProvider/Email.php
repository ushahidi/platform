<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Email Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Nexmo
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class DataProvider_Email extends DataProvider {

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Model_Contact::EMAIL;

	/**
	 * @return mixed
	 */
	public function send($to, $message, $title = "")
	{
		$provider_options = $this->options();

		$driver = $provider_options['outgoing_type'];
		$options = array(
			'hostname' => $provider_options['outgoing_server'],
			'port' => $provider_options['outgoing_port'],
			'encryption' =>
				($provider_options['outgoing_security'] != 'none') ?
				$provider_options['outgoing_security'] :
				'',
			'username' => $provider_options['outgoing_username'],
			'password' => $provider_options['outgoing_password']
			);

		$config = Kohana::$config->load('email');
		$config->set('driver', $driver);
		$config->set('options', $options);

		$tracking_id = self::tracking_id('email');

		$body = View::factory('email/layout');
		$body->message = $message;
		$body->tracking_id = $tracking_id;
		$body->site_url = rtrim(URL::site(), '/');

		$from = $this->from();
		$from_name = ! empty($provider_options['from_name']) ? $provider_options['from_name'] : $from;

		try
		{
			$result = Email::factory($title, $body->render(), 'text/html')
				->to($to)
				->from($from, $from_name)
				->send();

			return array(Message_Status::SENT, $tracking_id);
		}
		catch (Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
			// Failed
			return array(Message_Status::FAILED, FALSE);
		}

	}

	/**
	 * Fetch email messages from server
	 *
	 * For services where we have to poll for message (Twitter, Email, FrontlineSMS) this should
	 * poll the service and pass messages to $this->receive()
	 *
	 * @param  boolean $limit   maximum number of messages to fetch at a time
	 * @return int              number of messages fetched
	 */
	public function fetch($limit = FALSE)
	{
		$count = 0;

		$options = $this->options();
		$type = $options['incoming_type'];
		$server = $options['incoming_server'];
		$port = $options['incoming_port'];
		$encryption = $options['incoming_security'];
		$username = $options['incoming_username'];
		$password = $options['incoming_password'];

		// Encryption type
		$encryption = ($encryption != 'none') ? '/'.$encryption : '';

		try
		{
			// Try to connect
			// Use OP_SILENT to avoid errors when mailbox is empty.
			$connection = imap_open('{'.$server.':'.$port.'/'.$type.$encryption.'}INBOX', $username, $password, OP_SILENT);

			// Return on connection error
			if (! $connection)
			{
				Kohana::$log->add(Log::ERROR, "Could not connect to incoming email server");
				return 0;
			}

			$emails = imap_search($connection,'ALL');
			if ($emails)
			{
				// reverse sort emails?
				//rsort($emails);

				foreach($emails as $email_number)
				{
					// Break out if we've hit our limit
					// @todo revist and decide if this is worth doing when imap_search has grabbed everything anyway.
					if ($limit AND $i >= $limit)
						break;

					$overview = imap_fetch_overview($connection, $email_number, 0);
					$message = imap_fetchbody($connection, $email_number, 2);

					// Process the email
					$this->_process_incoming($overview[0], $message);

					// After processing, delete!
					imap_delete($connection, $email_number);

					$count++;
				}
			}
		}
		catch (Exception $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}

		return $count;
	}

	/**
	 * Process individual incoming email
	 *
	 * @param object $overview
	 * @param string message - the email message
	 */
	protected function _process_incoming($overview, $message)
	{
		$from = $this->_get_email($overview->from);
		$to = $this->_get_email($overview->to);
		$title = isset($overview->subject) ? $overview->subject : NULL;
		$message_id = isset($overview->message_id) ? $overview->message_id : NULL;

		// @todo revist hard coded HTML stripping & decoding
		// strip all html
		$message = trim(strip_tags($message, ""));
		// convert all HTML entities to their applicable characters
		$message = html_entity_decode($message, ENT_QUOTES, 'UTF-8');

		// Save the message
		$this->receive(Message_Type::EMAIL, $from, $message, $to, $title, $message_id);

		return;
	}

	/**
	 * Extract the FROM email address string
	 *
	 * @param string $from - from address string from email
	 * @return string email address or NULL
	 */
	protected function _get_email($from)
	{
		$pattern = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i';

		if ( preg_match_all($pattern, $from, $emails) )
		{
			foreach ($emails as $key => $value)
			{
				if (isset($value[0]))
				{
					return $value[0];
				}
			}
		}

		return NULL;
	}

}