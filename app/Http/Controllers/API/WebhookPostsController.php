<?php

namespace Ushahidi\App\Http\Controllers\API;

/**
 * Ushahidi API External Webhook Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Illuminate\Http\Request;
use Ushahidi\App\Http\Controllers\RESTController;
use Ushahidi\Core\Tool\Signer;

class WebhooksPosts extends RESTController
{

	protected function getResource()
	{
		return 'posts';
	}

	// protected function _is_auth_required()
	// {
	// 	return false;
	// }

	// public function checkApiKey($data)
	// {

	// 	if (isset($data['api_key'])) {
	// 		// Get api key and compare
	// 		return service('repository.apikey')->apiKeyExists($data['api_key']);
	// 	}

	// 	return false;
	// }

	// public function checkSignature($data)
	// {
	// 	$signature = $this->request->headers('X-Ushahidi-Signature');

	// 	if (isset($data['webhook_uuid']) && $signature) {

	// 		// Get webhook and validate signature
	// 		$webhook = service('repository.webhook')->getByUUID($data['webhook_uuid']);
	// 		$signer = new Signer($webhook->shared_secret);
	// 		$fullURL = URL::site(Request::detect_uri(), TRUE) . URL::query();

	// 		return $signer->validate($signature, $fullURL, $data);
	// 	}
	// 	return false;
	// }

	// public function before()
	// {
	// 	parent::before();

	// 	$post = $this->_request_payload;

	// 	if (!$this->checkApiKey($post) || !$this->checkSignature($post))
	// 	{
	// 		throw HTTP_Exception::factory(403, 'Forbidden');
	// 	}
	// }

	public function store(Request $request)
	{
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'webhook-update')
            ->setIdentifiers($this->getRouteParams($request))
            ->setPayload($request->json()->all());

        return $this->prepResponse($this->executeUsecase($request), $request);
	}
}
