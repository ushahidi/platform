<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API External Webhook Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Signer;

class Controller_Api_Webhooks_Posts extends Controller_Api_Posts {

	protected function _is_auth_required()
	{
		return false;
	}

	public function checkApiKey($data)
	{

		if (isset($data['api_key'])) {
			// Get api key and compare
			return service('repository.apikey')->apiKeyExists($data['api_key']);
		}

		return false;
	}

	public function checkSignature($data)
	{
		$signature = $this->request->headers('X-Ushahidi-Signature');

		if (isset($data['webhook_uuid']) && $signature) {

			// Get webhook and validate signature
			$webhook = service('repository.webhook')->getByUUID($data['webhook_uuid']);
			$signer = new Signer($webhook->shared_secret);
			$fullURL = URL::site(Request::detect_uri(), TRUE) . URL::query();

			return $signer->validate($signature, $fullURL, $data);
		}
		return false;
	}

	public function before()
	{
		parent::before();

		$post = $this->_request_payload;

		if (!$this->checkApiKey($post) || !$this->checkSignature($post))
		{
			throw HTTP_Exception::factory(403, 'Forbidden');
		}
	}

	public function action_put_index()
	{
		$this->_usecase = service('factory.usecase')
			->get($this->_resource(), 'webhook-update')
			->setIdentifiers($this->_identifiers())
			->setPayload($this->_payload());
	}
}
