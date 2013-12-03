<?php defined('SYSPATH') or die('No direct access allowed');

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\DataProvider
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
abstract class Ushahidi_DataProvider extends DataProvider_Core {

	/**
	 * @param  string  to Phone number to receive the message
	 * @param  string  message Message to be sent
	 */
	public function send($to, $message)
	{
		// noop, but must be defined or we hit errors in concrete DataProvider class
	}

	/**
	 * Receive Messages From data provider
	 *
	 * @param  string from from Phone number
	 * @param  string message Received Message
	 * @return void
	 */
	public function receive($from = NULL, $message = NULL, $title = NULL)
	{
		// Is the sender of the message a registered contact?
		$contact = Model_Contact::get_contact($from, 'phone');
		if ( ! $contact)
		{
			try
			{
				$contact = ORM::factory('Contact')
					->set('contact', $from)
					->set('type', 'phone')
					->set('data_provider', $this->provider_name())
					->save();
			}
			catch (ORM_Validation_Exception $e)
			{
				throw new Kohana_Exception(
					__("Failed to create contact. Errors: :error"),
					array(
						":error" => implode(',', Arr::flatten($e->errors('models')))
					)
				);
			}
		}

		if ( ! trim($message))
		{
			// HALT
			Kohana::$log->add(Log::ERROR, __("blank message received"));
			return;
		}

		// Save the message
		try
		{
			$message = ORM::factory('Message')
				->values(array(
					'message' => $message,
					'title' => $title,
					'contact_id' => $contact->id,
					'status' => 'received',
					'direction' => 'incoming',
					'type' => 'sms' // @todo stop hard coding this
				))
				->save();
		}
		catch (ORM_Validation_Exception $e)
		{
			throw new Kohana_Exception(
				__("Failed to create message. Errors: :error"),
				array(
					":error" => implode(',', Arr::flatten($e->errors('models')))
				)
			);
		}
	}

	/**
	 * Get queued outgoing messages
	 *
	 * @param  boolean $limit   maximum number of messages to return
	 * @return array            array of messages to be sent.
	 *                          Each element in the array should have 'to' and 'message' fields
	 */
	public function get_outgoing_messages($limit = FALSE)
	{
		$messages = array();

		//
		// Get All "Sent" SMSSync messages
		// Limit it to 20 MAX and FIFO
		$pings = ORM::factory('Message')
			->select('contacts.contact')
			->select('message.message')
			->join('contacts', 'INNER')
				->on('contact_id', '=', 'contacts.id')
			->where('status', '=', 'pending')
			->where('direction', '=', 'outgoing')
			->where('message.data_provider', '=', $this->provider_name())
			->order_by('created', 'ASC')
			->limit($limit)
			->find_all();

		foreach ($pings as $message)
		{
			$messages[] = array(
				'to' => $message->contact,
				'message' => $message->message,
				'message_id' => $message->id
				);

			// We'll update the pings status to 'unknown'
			// just so that its not picked up again
			// Also we don't know if the SMSSync from the phone
			// itself worked or not
			// @todo don't hard code this, receive status based on provider
			$message->status = 'unknown';
			$message->save();
		}

		return $messages;
	}

	public function set_message_status()
	{

	}
}