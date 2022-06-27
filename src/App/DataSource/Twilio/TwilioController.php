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

use Ushahidi\App\DataSource\DataSourceController;
use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ushahidi\Contracts\DataSource\MessageType as MessageType;
use Ushahidi\Core\Entity\Contact;

class TwilioController extends DataSourceController
{

    protected $source = 'twilio';

    /**
     * Handle incoming SMS from Twilio
     */
    public function handleRequest(Request $request)
    {
        // Authenticate the request
        if (!$this->source->verifySid($request->input('AccountSid'))) {
            abort(403, 'Incorrect or missing AccountSid');
        }

        // Detect contact type
        $to = $request->input('To');
        $from = $request->input('From');
        if (strpos($from, ':')) {
            $channel = (explode(':', $from))[0];
            switch ($channel) {
                case 'whatsapp':
                    $contact_type = Contact::WHATSAPP;
                    break;
                default:
                    $contact_type = Contact::PHONE;
                    break;
            }
        } else {
            $contact_type = Contact::PHONE;
        }

        // Remove Non-Numeric characters because that's what the DB has
        $to = preg_replace("/[^0-9,+.]/", "", $to);
        $from  = preg_replace("/[^0-9,+.]/", "", $from);

        $message_text = $request->input('Body');
        $message_sid  = $request->input('MessageSid');

        // @todo use other info from twillio, ie: location, media

        $this->save([
            'type' => MessageType::SMS,
            'from' => $from,
            'to' => $to,
            'contact_type' => $contact_type,
            'message' => $message_text,
            'title' => null,
            'data_source_message_id' => $message_sid,
            'data_source' => 'frontlinesms'
        ]);

        // If we have an auto response configured, return the response messages
        if ($this->source->getSmsAutoResponse()) {
            return response(
                <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Response>
<Message>{$this->source->getSmsAutoResponse()}</Message>
</Response>
XML
            )
                ->header('Content-Type', 'text/xml');
        }
    }
}
