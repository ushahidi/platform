<?php

namespace Ushahidi\Addons\HttpSMS;

/**
 * HttpSMS Callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Addons\HttpSMS
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Illuminate\Http\Request;
use Ushahidi\Contracts\Contact;
use Ushahidi\DataSource\Contracts\MessageType;
use Ushahidi\DataSource\DataSourceController;

class HttpSMSController extends DataSourceController
{
    /** @var string|HttpSMS */
    protected $source = 'httpsms';

    public function handleRequest(Request $request)
    {
        $eventType = $request->header('X-Event-Type');
        $authorization = $request->header('Authorization');
        $contentType = $request->header('Content-Type');

        try {
            /**  */
            $this->source->verifyToken($authorization);
            // Token is valid, continue processing
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        if ($contentType === 'application/json') {
            $eventPayload = $request->all();
            $eventData = $eventPayload['data'];

            // Remove Non-Numeric characters because that's what the DB has
            $from  = preg_replace("/[^0-9,+.]/", "", $eventData['contact']);

            // Handle the webhook event based on $eventType
            switch ($eventType) {
                case "message.phone.received":
                    $data = [
                        'type' => MessageType::SMS,
                        'from' => $from,
                        'contact_type' => Contact::PHONE,
                        'message' => $eventData['content'],
                        'to' => $request->input('to'),
                        'title' => null,
                        'datetime' => $eventPayload->getTime()->format('Y-m-d H:i:s') ?? null,
                        'data_source_message_id' => $eventData['message_id'],
                        'data_source' => 'HttpSMS',
                        'additional_data' => [
                            'id' => $eventPayload[''],
                            'owner' => $eventData['owner'],
                            'request_id' => $eventData['request_id'] ?? null,
                            'user_id' => $eventData['user_id'] ?? null,
                            'sim' => $eventData['sim'] ?? null
                        ]
                    ];
                    break;

                case "message.phone.delivered":
                    # code...
                    break;
            }
            // Add more event types as needed

            $this->save($data);

            // Then return success

            return response()->json(['message' => 'Webhook event processed successfully']);
        }

        return response()->json(['error' => 'Invalid request'], 401);
    }
}
