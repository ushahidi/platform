<?php

namespace Ushahidi\App\DataSource\Email;

/**
 * Email Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Email
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\IncomingAPIDataSource;
use Ushahidi\App\DataSource\OutgoingAPIDataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Illuminate\Contracts\Mail\Mailer;
use Ushahidi\Core\Entity\Contact;
use Log;

class Email implements IncomingAPIDataSource, OutgoingAPIDataSource
{

	protected $config;
	protected $mailer;

	/**
	 * Constructor function for DataSource
	 */
	public function __construct(array $config, Mailer $mailer = null, $siteConfig = null, $clientUrl = null)
	{
		$this->config = $config;
		$this->mailer = $mailer;
		// @todo figure out a better way to set these. Maybe globally for all emails?
		$this->siteConfig = $siteConfig;
		$this->clientUrl = $clientUrl;
	}

	public function getName()
	{
		return 'Email';
	}

	public function getId()
	{
		return strtolower($this->getName());
	}

	public function getServices()
	{
		return [MessageType::EMAIL];
	}

	public function getOptions()
	{
		return array(
			'intro_text' => array(
				'label' => '',
				'input' => 'read-only-text',
				'description' => 'In order to receive reports by email, please input your email account settings below'
			),
			'incoming_type' => array(
				'label' => 'Incoming Server Type',
				'input' => 'radio',
				'description' => '',
				'options' => array('POP', 'IMAP'),
				'rules' => array('required', 'number')
			),
			'incoming_server' => array(
				'label' => 'Incoming Server',
				'input' => 'text',
				'description' => '',
				'description' => 'Examples: mail.yourwebsite.com, imap.gmail.com, pop.gmail.com',
				'rules' => array('required')
			),
			'incoming_port' => array(
				'label' => 'Incoming Server Port',
				'input' => 'text',
				'description' => 'Common ports: 110 (POP3), 143 (IMAP), 995 (POP3 with SSL), 993 (IMAP with SSL)',
				'rules' => array('required','number')
			),
			'incoming_security' => array(
				'label' => 'Incoming Server Security',
				'input' => 'radio',
				'description' => '',
				'options' => array('None', 'SSL', 'TLS')
			),
			'incoming_username' => array(
				'label' => 'Incoming Username',
				'input' => 'text',
				'description' => '',
				'placeholder' => 'Email account username',
				'rules' => array('required')
			),
			'incoming_password' => array(
				'label' => 'Incoming Password',
				'input' => 'text',
				'description' => '',
				'placeholder' => 'Email account password',
				'rules' => array('required')
			)
		);
	}

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Contact::EMAIL;

	/**
	 * @return mixed
	 */
	public function send($to, $message, $title = "")
	{
		$site_name = $this->siteConfig['name'];
		$site_email = $this->siteConfig['email'];
		$multisite_email = config('multisite.email');

		// @todo make this more robust
		if ($multisite_email) {
			$from_email = $multisite_email;
		} elseif ($site_email) {
			$from_email = $site_email;
		} else {
			$from_email = false;
			// Get host from lumen
			// $host = app()->make('request')->getHost();
			// $from_email = 'noreply@' . $host;
		}

		try {
			$this->mailer->send(
				'emails/outgoing-message',
				[
					'message' => $message,
					'site_url' => $this->clientUrl
				],
				function ($message) use ($to, $title, $from_email, $site_name) {
					$message->to($to);
					$message->subject($title);
					if ($from_email) {
						$message->from($from_email, $site_name);
					}
				}
			);

			return array(MessageStatus::SENT, false);
		} catch (\Exception $e) {
			app('log')->error($e->getMessage());
			// Failed
			return array(MessageStatus::FAILED, false);
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
	public function fetch($limit = false)
	{
		$messages = [];

		$limit = 200;

		$type = $this->config['incoming_type'];
		$server = $this->config['incoming_server'];
		$port = $this->config['incoming_port'];
		$encryption = $this->config['incoming_security'];
		$username = $this->config['incoming_username'];
		$password = $this->config['incoming_password'];

		// Encryption type
		$encryption = (strcasecmp($encryption, 'none') != 0) ? '/'.$encryption : '';

		try {
			// Try to connect
			$connection = imap_open('{'.$server.':'.$port.'/'.$type.$encryption.'}INBOX', $username, $password);

			// Return on connection error
			if (! $connection) {
				app('log')->error("Could not connect to incoming email server");
				return [];
			}

			$last_uid = service('repository.message')->getLastUID('email');
			$max_range = $last_uid + $limit;
			$search_string = $last_uid ? $last_uid + 1 . ':' . $max_range : '1:' . $max_range;

			$emails = imap_fetch_overview($connection, $search_string, FT_UID);

			if ($emails) {
				// reverse sort emails?
				//rsort($emails);
				foreach ($emails as $email) {
					// Break out if we've hit our limit
					// @todo revist and decide if this is worth doing when imap_search has grabbed everything anyway.
					if ($limit and count($messages) >= $limit) {
						break;
					}

					$message = $html_message = "";
					$structure = imap_fetchstructure($connection, $email->uid, FT_UID);

					// Get HTML message from multipart message
					if (! empty($structure->parts)) {
						$no_of_parts = count($structure->parts);

						foreach ($structure->parts as $part_number => $part) {
							if ($part->subtype == 'HTML') {
								$html_message .= imap_fetchbody($connection, $email->uid, $part_number, FT_UID);
							} elseif ($part->subtype == 'PLAIN') {
								$message .= imap_fetchbody($connection, $email->uid, $part_number, FT_UID);
							}
						}
					} else {
						// or just fetch the body if not a multipart message
						$message = imap_body($connection, $email->uid, FT_UID);
					}


					// Process the email
					if (! empty($html_message)) {
						$html_message = imap_qprint($html_message);
						$messages[] = $this->processIncoming($email, $html_message);
					} elseif (! empty($message)) {
						$message = imap_qprint($message);
						$messages[] = $this->processIncoming($email, $message);
					}
				}
			}

			imap_errors();

			imap_close($connection);
		} catch (\Exception $e) {
			$errors = imap_errors();
			$errors = is_array($errors) ? implode(', ', $errors) : "";
			app('log')->error($e->getMessage(), [':errors' => $errors]);
		}

		return $messages;
	}

	/**
	 * Process individual incoming email
	 *
	 * @param object $overview
	 * @param string message - the email message
	 */
	protected function processIncoming($overview, $message)
	{
		$from = $this->getEmail($overview->from);
		$to = isset($overview->to) ? $this->getEmail($overview->to) : $this->from();
		$title = isset($overview->subject) ? $overview->subject : null;
		$data_source_message_id = isset($overview->uid) ? $overview->uid : null;
		// @todo revist hard coded HTML stripping & decoding
		// strip all html

		$message = trim(strip_tags($message, ""));
		// convert all HTML entities to their applicable characters
		$message = html_entity_decode($message, ENT_QUOTES, 'UTF-8');
		if ($message) {
			// Save the message
			return [
				'type' => DataSource\Message\Type::EMAIL,
				'contact_type' => Contact::EMAIL,
				'from' => $from,
				'message' => $message,
				'to' => $to,
				'title' => $title,
				'data_source_message_id' => $data_source_message_id,
				'additional_data' => $additional_data
			];
		}

		return [];
	}

	/**
	 * Extract the FROM email address string
	 *
	 * @param string $from - from address string from email
	 * @return string email address or NULL
	 */
	protected function getEmail($from)
	{
		$pattern = '/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i';

		if (preg_match_all($pattern, $from, $emails)) {
			foreach ($emails as $key => $value) {
				if (isset($value[0])) {
					return $value[0];
				}
			}
		}

		return null;
	}
}
