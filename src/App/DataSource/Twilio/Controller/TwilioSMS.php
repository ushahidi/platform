<?php

namespace Ushahidi\App\DataSource\Twilio;

/**
 * Twilio SMS Callback Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ushahidi\App\DataSource\Message\Type as MessageType;

class TwilioSMS extends Controller
{

    /**
     * Handle incoming SMS from Twilio
     */
    public function reply(Request $request)
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

        $message_text = $request->input('Body');
        $message_sid  = $request->input('MessageSid');

        // @todo use other info from twillio, ie: location, media

        $provider->receive(MessageType::SMS, $from, $message_text, $to, null, $message_sid);

        // If we have an auto response configured, return the response messages
        if (! empty($options['sms_auto_response'])) {
            return response(
                    view(__DIR__ . '/../views/sms_response', ['response' => $config['sms_auto_response']])
                )
                ->header('Content-Type', 'text/xml');
        }
    }
}
