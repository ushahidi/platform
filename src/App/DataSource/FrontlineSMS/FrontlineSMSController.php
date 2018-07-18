<?php

namespace Ushahidi\App\DataSource\FrontlineSMS;

/**
 * FrontlineSms Callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\FrontlineSms
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\DataSourceController;
use Illuminate\Http\Request;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\Core\Entity\Contact;

class FrontlineSMSController extends DataSourceController
{

    protected $source = 'frontlinesms';

    public function handleRequest(Request $request)
    {
        // Authenticate the request
        if (!$this->source->verifySecret($request->input('secret'))) {
            return abort(403, 'Incorrect or missing secret key');
        }

        $from = $request->input('from');

        if (empty($from)) {
            abort(400, 'Missing from');
        }

        $message_text = $request->input('message');

        if (empty($message_text)) {
            abort(400, 'Missing message');
        }

        // Allow for Alphanumeric sender
        $from = preg_replace("/[^0-9A-Za-z+ ]/", "", $from);

        $this->save([
            'type' => MessageType::SMS,
            'from' => $from,
            'contact_type' => Contact::PHONE,
            'message' => $message_text,
            'title' => null,
            'data_source' => 'frontlinesms'
        ]);

        return ['payload' => [
            'success' => true,
            'error' => null
        ]];
    }
}
