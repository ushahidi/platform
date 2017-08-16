<?php

namespace Ushahidi\App\DataSource\FrontlineSMS\Controller;

/**
 * FrontlineSms Callback controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\FrontlineSms
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;

class FrontlineSMS extends Controller
{
	protected $_provider = null;

	protected $_json = [];

	protected $options = null;

	public function index(Request $request)
	{
        $source = app('datasources')->getEnabledSources('frontlinesms');
        if (!$source) {
            abort(403, 'The FrontlineSMS data source is not currently available.');
        }

        // Authenticate the request
        if (!$source->verifySecret($request->input('secret'))) {
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
		$from = preg_replace("/[^0-9A-Za-z ]/", "", $from);

		$source->receive(MessageType::SMS, $from, $message_text);

		return ['payload' => [
			'success' => true,
			'error' => null
		]];
	}
}
