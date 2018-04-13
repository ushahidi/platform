<?php defined('SYSPATH') or die('No direct access allowed');

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\DataProvider
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\Post;

abstract class Ushahidi_DataProvider extends DataProvider_Core {

	/**
	 * @param  string  to Phone number to receive the message
	 * @param  string  message Message to be sent
	 */
	public function send($to, $message, $title = "")
	{
		// noop, but must be defined or we hit errors in concrete DataProvider class
	}

	/**
	 * Receive Messages From data provider
	 *
	 * @param  string type    Message type
	 * @param  string from    From contact
	 * @param  string message Received Message
	 * @param  string to      To contact
	 * @param  string title   Received Message title
	 * @param  string data_provider_message_id Message ID
	 * @return void
	 */
	public function receive($type, $from, $message, $to = NULL, $title = NULL, $date = NULL, $data_provider_message_id = NULL, Array $additional_data = NULL)
	{
		$data_provider = $this->provider_name();
		$contact_type = $this->contact_type;

		$usecase = service('factory.usecase')->get('messages', 'receive');
		try
		{
			$usecase->setPayload(compact(['type', 'from', 'message', 'to', 'title', 'date', 'data_provider_message_id', 'data_provider', 'contact_type', 'additional_data']))
				->interact();
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
			throw new HTTP_Exception_400('Validation Error: \':errors\'', array(
				':errors' => implode(', ', Arr::flatten($e->getErrors())),
			));
		}
		catch (\InvalidArgumentException $e)
		{
			throw new HTTP_Exception_400('Bad request: :error', array(
				':error' => $e->getMessage(),
			));
		}
	}

	/**
	 * Get queued outgoing messages
	 *
	 * @param  boolean $limit   maximum number of messages to return
	 * @param  mixed   $current_status  Current status of messages
	 * @param  mixed   $new_status  New status to save for message, FALSE to leave status as is
	 * @return array            array of messages to be sent.
	 *                          Each element in the array should have 'to' and 'message' fields
	 */
	public function get_pending_messages($limit = FALSE, $current_status = Message_Status::PENDING_POLL, $new_status = Message_Status::UNKNOWN)
	{
		$message_repo = service('repository.message');
		$contact_repo = service('repository.contact');
		$messages = array();
		$provider = $this->provider_name;

		// Get All "Sent" SMSSync messages
		// Limit it to 20 MAX and FIFO
		$pings = $message_repo->getPendingMessages($current_status, $provider, $limit);

		foreach ($pings as $message)
		{
			$contact = $contact_repo->get($message->contact_id);
			$messages[] = array(
				'to' => $contact->contact, // @todo load this in the message?
				'message' => $message->message,
				'message_id' => $message->id
				);

			// Update the message status
			if ($new_status)
			{
				$message->setState([
						'status' => $new_status
					]);
				$message_repo->update($message);
			}
		}

		return $messages;
	}

	/**
	 * Process pending messages for provider
	 *
	 * For services where we can push messages (rather than being polled like SMS Sync):
	 * this should grab pending messages and pass them to send()
	 *
	 * @param  boolean $limit   maximum number of messages to send at a time
	 */
	public static function process_pending_messages($limit = 20, $provider = FALSE)
	{
		$message_repo = service('repository.message');
		$contact_repo = service('repository.contact');
		$providers = array();
		$count = 0;

		// Grab latest messages
		$pings = $message_repo->getPendingMessages(Message_Status::PENDING, $provider, $limit);

		foreach($pings as $message)
		{
			$provider = DataProvider::factory($message->data_provider, $message->type);

			// Load contact
			$contact = $contact_repo->get($message->contact_id);

			// Send message and get new status/tracking id
			list($new_status, $tracking_id) = $provider->send($contact->contact, $message->message, $message->title);

			// Update message details
			$message->setState([
					'status' => $new_status,
					'data_provider' => $provider->provider_name(),
					'data_provider_message_id' => $tracking_id ?: null
				]);

			// @todo handle errors
			$message_repo->update($message);

			$count ++;
		}

		return $count;
	}

}
