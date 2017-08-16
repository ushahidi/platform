<?php

namespace Ushahidi\App\DataSource\Twilio;

/**
 * Twilio IVR Callback Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Ushahidi\App\DataSource\Message\Type as MessageType;

class TwilioIvr extends Controller
{

	/**
	 * Script for incoming calls to Twilio
	 */
	public function index(Request $request)
	{
		header('Content-type: text/xml');

		echo '<?xml version="1.0" encoding="UTF-8"?>
		<Response>
			<Gather action="'.url('ivr/twilio/gather').'" method="POST" numDigits="1" timeout="15">
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
	public function gather(Request $request)
	{
        $source = app('datasources')->getEnabledSources('twilio');
        if (!$source) {
            abort(403, 'The Twilio data source is not currently available.');
        }

		// Authenticate the request
        if (!$source->verifySid($request->input('AccountSid'))) {
            abort(403, 'Incorrect or missing AccountSid');
        }

		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $request->input('To'));
		$from  = preg_replace("/[^0-9,.]/", "", $request->input('From'));
		$message_sid  = $request->input('CallSid');

		$digits  = $request->input('Digits');
		if ($digits == 1) {
			$message_text = 'IVR: Okay';
		} elseif ($digits == 2) {
			$message_text = 'IVR: Not Okay';
		} else {
			// HALT
			Log::error("Not a valid IVR response", array("digits" => $digits));
			return;
		}

		$provider->receive(MessageType::IVR, $from, $message_text, $to, null, $message_sid);
	}
}
