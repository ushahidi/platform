<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Twilio IVR Callback Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Ivr_Twilio extends Controller {

	/**
	 * Script for incoming calls to Twilio
	 */
	public function action_index()
	{
		header('Content-type: text/xml');

		echo '<?xml version="1.0" encoding="UTF-8"?>
		<Response>
			<Gather action="'.URL::base().'ivr/twilio/gather" method="POST" numDigits="1" timeout="15">
				<Say voice="woman">Thank you for calling Ushahidi.com</Say>
				<Pause length="1"/>
				<Say voice="woman">If you are okay, please press 1 then press pound or hash</Say>
				<Pause length="1"/>
				<Say voice="woman">If you are not okay, please press 2 then press pound or hash</Say>
			</Gather>
			<Say voice="woman">We didn\'t receive any input. Goodbye!</Say>
			<Hangup/>
		</Response>
		';
	}

	/**
	 * Callback for 'gather' response on call to Twilio
	 */
	public function action_gather()
	{
		if ($this->request->method() != 'POST')
		{
			// Only POST is allowed
			throw HTTP_Exception::factory(405, 'The :method method is not supported. Supported methods are :allowed_methods', array(
					':method'          => $this->request->method(),
					':allowed_methods' => Http_Request::POST,
				))
				->allowed(Http_Request::POST);
		}

		$provider = DataProvider::factory('twilio');

		// Authenticate the request
		$options =  $provider->options();
		if ($this->request->post('AccountSid') !== $options['account_sid'])
		{
			// Could not authenticate the request?
			throw HTTP_Exception::factory(403, 'Incorrect or missing AccountSid');
		}

		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $this->request->post('To'));
		$from  = preg_replace("/[^0-9,.]/", "", $this->request->post('From'));
		$message_sid  = $this->request->post('CallSid');

		$digits  = $this->request->post('Digits');
		if ($digits == 1)
		{
			$message_text = 'IVR: Okay';
		}
		else if ($digits == 2)
		{
			$message_text = 'IVR: Not Okay';
		}
		else
		{
			// HALT
			Kohana::$log->add(Log::ERROR, __("':digits' is not a valid IVR response", array(":digits" => $digits)));
			return;
		}

		$provider->receive(Message_Type::IVR, $from, $message_text, $to, NULL, $message_sid);
	}
}