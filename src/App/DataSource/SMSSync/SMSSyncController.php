<?php

namespace Ushahidi\App\DataSource\SMSSync;

/**
 * SMS Sync Callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\SMSSync
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\DataSourceController;
use Illuminate\Http\Request;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\Core\Entity\Contact;

class SMSSyncController extends DataSourceController
{

    protected $source = 'smssync';

    public function handleRequest(Request $request)
    {
        // Authenticate the request
        if (!$this->source->verifySecret($request->input('secret'))) {
            return response(['payload' => [
                    'success' => false,
                    'error' => 'Incorrect or missing secret key'
                ]], 403);
        }

        // Process incoming messages from SMSSync only if the request is POST
        if ($request->method() == 'POST') {
            return $this->incoming($request);
        }

        // Attempt Task if request is GET and task type is 'send'
        if ($request->method() == 'GET' and $request->input('task') == 'send') {
            return $this->task($request);
        }

        // Set the response
        return ['payload' => [
            'success' => true,
            'error' => null
        ]];
    }

    /**
     * Process messages received from SMSSync
     */
    private function incoming($request)
    {
        $from = $request->input('from');

        if (empty($from)) {
            return response(['payload' => [
                        'success' => false,
                        'error' => 'Missing from value'
                    ]], 400);
        }

        $message_text = $request->input('message');

        if (empty($message_text)) {
            return response(['payload' => [
                    'success' => false,
                    'error' => 'Missing message'
                ]], 400);
        }

        // Remove Non-Numeric characters because that's what the DB has
        $to = preg_replace("/[^0-9,+.]/", "", $request->input('sent_to'));

        // Allow for Alphanumeric sender
        $from = preg_replace("/[^0-9A-Za-z+ ]/", "", $from);

        $date = $request->input('sent_timestamp');

        $this->save([
            'type' => MessageType::SMS,
            'from' => $from,
            'contact_type' => Contact::PHONE,
            'message' => $message_text,
            'title' => null,
            'datetime' => $date,
            'data_source' => 'smssync'
        ]);

        return ['payload' => [
            'success' => true,
            'error' => null
        ]];
    }

    /**
     * Implement SMSSync task feature to allow SMSSync to send messages as SMS
     * to users.
     */
    private function task($request)
    {
        // Do we have any tasks for SMSSync?
        // Grab messages to send, 20 at a time.
        $messages = $this->getPendingMessages(20);

        return ['payload' => [
            'task' => "send",
            'success' => true,
            'secret' => $this->source->getSecret(),
            'messages' => array_values(array_map(function ($message) {
                // Reformat message for SMSSYnc
                return [
                    'to' => $message->contact,
                    'message' => $message->message,
                    'message_id' => $message->id,
                    'uuid' => $message->data_source_message_id
                ];
            }, $messages)),
        ]];
    }
}
