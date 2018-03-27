<?php

namespace Ushahidi\App\DataSource\AfricasTalking;

/**
 * AfricasTalking Callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\AfricasTalking
 * @copyright  2018 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\DataSourceController;
use Illuminate\Http\Request;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\Core\Entity\Contact;

class AfricasTalkingController extends DataSourceController
{

    protected $source = 'africastalking';

	public function handleRequest(Request $request)
	{
        // Authenticate the request
        if (!$this->source->verifySecret($request->input('secret'))) {
            return abort(403, 'Incorrect or missing secret key');
		}

		if (empty($from)) {
			abort(400, 'Missing from');
		}

		$message_text = $request->input('text');

		if (empty($message_text)) {
			abort(400, 'Missing message');
		}

		// Allow for Alphanumeric sender
		$from = preg_replace("/[^0-9A-Za-z ]/", "", $from);

		$this->save([
			'type' => MessageType::SMS,
			'from' => $from,
			'contact_type' => Contact::PHONE,
			'message' => $message_text,
			'title' => null,
			'data_source' => 'africastalking'
		]);

		return ['payload' => [
			'success' => true,
			'error' => null
		]];
	}
}
