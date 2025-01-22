<?php

namespace Ushahidi\Addons\Infobip;

/**
 * InfobipSMS Callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Addons\Infobip
 * @copyright  2023 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Illuminate\Http\Request;
use Ushahidi\Contracts\Contact;
use Ushahidi\DataSource\Contracts\MessageType;
use Ushahidi\DataSource\DataSourceController;

class InfobipSMSController extends DataSourceController
{
    protected $source = 'infobip';

    public function handleRequest(Request $request)
    {
        $results = collect($request->input('results'));

        $results->each(function ($result) {
            $data = [
                'type' => MessageType::SMS,
                'from' => $result['from'],
                'contact_type' => Contact::PHONE,
                'message' => $result['text'],
                'to' => $result['to'],
                'title' => null,
                'datetime' => $result['receivedAt'] ?? null,
                'data_source_message_id' => $result['messageId'],
                'data_source' => 'InfobipSMS',
                'additional_data' => [
                ]
            ];

            $this->save($data);
        });

        return response()->json(['status' => 'ok']);
    }
}
