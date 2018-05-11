<?php

namespace Ushahidi\App\DataSource\Nexmo;

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\DataSource
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\App\DataSource\DataSourceController;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NexmoController extends DataSourceController
{

    protected $source = 'nexmo';

    public function handleRequest(Request $request)
    {
        $message = \Nexmo\Message\InboundMessage::createFromGlobals();
        if (!$message->isValid() || !$message->getBody() || !$message->getFrom()) {
            throw abort(400, "Invalid message");
        }

        // Remove Non-Numeric characters because that's what the DB has
        $to = preg_replace("/[^0-9,+.]/", "", $message->getTo());
        $from  = preg_replace("/[^0-9,+.]/", "", $message->getFrom());

        $this->save([
            'type' => MessageType::SMS,
            'from' => $from,
            'contact_type' => Contact::PHONE,
            'message' => $message->getBody(),
            'to' => $to,
            'title' => null,
            'data_source_message_id' => $message->getMessageId(),
            'data_source' => 'nexmo'
        ]);

        // Then return success
        return [
            'success' => true
        ];
    }
}
