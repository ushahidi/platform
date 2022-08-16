<?php

namespace Ushahidi\Addons\AfricasTalking;

/**
 * AfricasTalking callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Addons\AfricasTalking
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Illuminate\Http\Request;
use Ushahidi\Contracts\Contact;
use Ushahidi\DataSource\Contracts\MessageType;
use Ushahidi\DataSource\DataSourceController;

class ShortMessageController extends DataSourceController
{
    protected $source = 'africastalking';

    public function handleRequest(Request $request)
    {
        // Remove Non-Numeric characters because that's what the DB has
        $from  = preg_replace("/[^0-9,+.]/", "", $request->input('from'));

        $this->save([
            'type' => MessageType::SMS,
            'from' => $from,
            'contact_type' => Contact::PHONE,
            'message' => $request->input('text'),
            'to' => $request->input('to'),
            'title' => null,
            'datetime' => $request->input('date'),
            'data_source_message_id' => $request->input('id'),
            'data_source' => 'africastalking',
            'additional_data' => [
                'linkId' => $request->input('linkId'),
                'date' => $request->input('date')
            ]
        ]);

        // Then return success
        return [
            'success' => true
        ];
    }
}
