<?php

namespace Ushahidi\Addons\Mteja;

/**
 * Mteja callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Addons\Mteja
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid as UUID;
use Ushahidi\Contracts\Contact;
use Ushahidi\DataSource\Contracts\MessageType;
use Ushahidi\DataSource\DataSourceController;

class ShortMessageController extends DataSourceController
{
    protected $source = 'mteja';

    public function handleRequest(Request $request)
    {
        // Remove Non-Numeric characters because that's what the DB has
        $from  = preg_replace("/[^0-9,+.]/", "", $request->input('customerNumber'));

        $this->save([
            'type' => MessageType::SMS,
            'from' => $from,
            'contact_type' => Contact::PHONE,
            'message' => $request->input('text'),
            'to' => $request->input('to'),
            'title' => null,
            'datetime' => $request->input('date'),
            'data_source_message_id' => $request->input('virtualNumber'),
            'data_source' => 'mteja',
            'additional_data' => [
                'appId' => $request->input('appId'),
                // 'questionText' => $request->input('questionText')
            ]
        ]);

        // Then return success
        return [
            'success' => true
        ];
    }
}
