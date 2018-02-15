<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Export Execute CLI Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class Controller_Api_Exports_External_Cli extends Ushahidi_Rest {

	protected function _scope()
	{
		//return 'export_jobs';
		return null;
	}

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

	// 	if ($signature) {
	// 		//Validate signature
	// 		$shared_secret = getenv('PLATFORM_SHARED_SECRET');
	// 		$signer = new Signer($shared_secret);
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

	protected function _is_auth_required()
	{
		return false;
	}

	public function action_get_index_collection($command = 'export')
	{

		// Get Symfony console app
		$app = service('app.console');
		$command = $app->get('exporter');
		$limit = 0;
		$offset = 0;

		// Construct console command input
		$input = new ArrayInput(array(
			'action' => 'export',
			'--limit' => $limit,
			'--offset' => $offset,
		 ), $command->getDefinition());
		 

		// Create Output Buffer
		$output = new BufferedOutput();

		
		// Run the command
		$command->run($input, $output);

		//Retrieve results
		$export_results = $output->fetch();

		$this->_response_payload = [
			'results'	=> explode("\n", $export_results, -1),
		];
	}

	public function action_post_export_collection()
	{
		$this->action_get_index_collection();
	}

}
