<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Twilio Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class DataProvider_Twilio extends DataProvider {

	/**
	 * Client to talk to the Twilio API
	 *
	 * @var Services_Twilio
	 */
	private $_client;

	/**
	 * @return mixed
	 */
	public function send($to, $message)
	{
		include_once Kohana::find_file('vendor', 'twilio/Services/Twilio');

		if ( ! isset($this->_client))
		{
			$this->_client = new Services_Twilio($this->_options['account_sid'], $this->_options['auth_token']);
		}

		// Send!
		try
		{
			$message = $this->_client->account->messages->sendMessage($this->_from, '+'.$to, $message);
			return array(Message_Status::SENT, $message->sid);
		}
		catch (Services_Twilio_RestException $e)
		{
			Kohana::$log->add(Log::ERROR, $e->getMessage());
		}

		return array(Message_Status::FAILED, FALSE);
	}
}