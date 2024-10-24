<?php

namespace Ushahidi\DataSource\Sislog;

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\DataSource
 * @copyright  2024 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\DataSource\DataSourceController;
use Ushahidi\Contracts\Contact;
use Ushahidi\DataSource\Contracts\MessageType;
use Illuminate\Http\Request;

class SislogController extends DataSourceController
{
    protected $source = 'sislog';

    public function handleRequest(Request $request)
    {
        //// Authenticate the request
        // if (!$this->source->verifySecret($request->input('secret'))) {
        //     return response(['payload' => [
        //         'success' => false,
        //         'error' => 'Incorrect or missing secret key'
        //     ]], 403);
        // }

        // Process incoming messages from Sislog only if the request is POST
        if ($request->method() == 'POST') {
            return $this->incoming($request);
        }


        // Set the response
        return ['payload' => [
            'success' => true,
            'error' => null
        ]];
    }

    /**
     * Process messages received from Sislog
     */
    private function incoming($request)
    {
        $from = $request->input('msisdn');

        if (empty($from)) {
            return response(['payload' => [
                'success' => false,
                'error' => 'Missing from value'
            ]], 400);
        }

        $message_text = $request->input('msg');

        if (empty($message_text)) {
            return response(['payload' => [
                'success' => false,
                'error' => 'Missing message'
            ]], 400);
        }

        // Allow for Alphanumeric sender
        $from = preg_replace("/[^0-9A-Za-z+ ]/", "", $from);


        $this->save([
            'type' => MessageType::SMS,
            'from' => $from,
            'contact_type' => Contact::PHONE,
            'message' => $message_text,
            'title' => null,
            'data_source' => 'sislog'
        ]);

        return ['payload' => [
            'success' => true,
            'error' => null
        ]];
    }
}
