<?php

namespace Ushahidi\App\DataSource;

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\DataSource
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\Post;
use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class DataSourceController extends Controller
{

	protected $source;

	public function __construct()
    {
		$this->source = app('datasources')->getSource($this->source);
	}

	abstract public function handleRequest(Request $request);
	/*public function handleRequest(Request $request, $source) {
		// Get the datasource
        $source = app('datasources')->getEnabledSources($source);
        if (!$source) {
            abort(400, 'Data source is not currently available.');
        }

        // Transform the request to a message payload
		$payload = $source->receive($request);

		// And save that payload
		$this->save($payload);

		// Then return success
		return [
			'success' => true
		];
	}*/

	/**
	 * Receive Messages From data provider
	 *
	 * @param  array  $payload Message payload containing:
	 *     - string type    Message type
	 *     - string from    From contact
	 *     - string message Received Message
	 *     - string to      To contact
	 *     - string title   Received Message title
	 *     - string data_provider_message_id Message ID
	 * @return void
	 */
	protected function save($payload)
    {
		$usecase = service('factory.usecase')->get('messages', 'receive');
		try {
			return $usecase->setPayload($payload)
				->interact();
		} catch (\Ushahidi\Core\Exception\NotFoundException $e) {
			abort(404, $e->getMessage());
		} catch (\Ushahidi\Core\Exception\AuthorizerException $e) {
			abort(403, $e->getMessage());
		} catch (\Ushahidi\Core\Exception\ValidatorException $e) {
			abort(422, 'Validation Error: ' . $e->getMessage() . '; ' .  implode(', ', $e->getErrors()));
		} catch (\InvalidArgumentException $e) {
			abort(400, 'Bad request: ' . $e->getMessage() . '; ' . implode(', ', $e->getErrors()));
		}
	}
}
