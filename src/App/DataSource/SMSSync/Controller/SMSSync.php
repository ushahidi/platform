<?php

namespace Ushahidi\App\DataSource\SMSSync\Controller;

/**
 * SMS Sync Callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\SMSSync
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;

class SMSSync extends Controller
{

    protected $source = null;

    public function index(Request $request)
    {
        $this->source = app('datasources')->getEnabledSources('smssync');
        if (!$this->source) {
            response(['payload' => [
                    'success' => false,
                    'error' => 'SMSSync is not enabled'
                ]], 403);
        }

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
        $to = preg_replace("/[^0-9,.]/", "", $request->input('sent_to'));

        // Allow for Alphanumeric sender
        $from = preg_replace("/[^0-9A-Za-z ]/", "", $from);

        $this->source->receive(MessageType::SMS, $from, $message_text, $to);

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
        //
        // We don't know if the SMS from the phone itself work or not,
        // but we'll update the messages status to 'unknown' so that
        // its not picked up again
        $messages = $this->source->get_pending_messages(20, MessageStatus::PENDING_POLL, MessageStatus::UNKNOWN);

        return ['payload' => [
            'task' => "send",
            'success' => true,
            'messages' => $messages,
            //'secret' => $this->options['secret'] ?: null
        ]];
    }
}
