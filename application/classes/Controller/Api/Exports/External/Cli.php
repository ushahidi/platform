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

class Controller_Api_Exports_External_Cli extends Controller_Api_External {

	protected function _scope()
	{
		return 'export_jobs';
	}

	public function action_get_index()
	{
		// Get Symfony console app
		$app = service('app.console');
		$command = $app->get('exporter');

		$job_id = $this->request->param('id');

		//Deal with query string -
		// init and assume unset
		$limit = 0;
		$offset = 0;
		$add_header = true;
		// then do some validation (remove this if Kohana is better at this)
		if (is_numeric($this->request->query('limit')))
		{
			$limit = $this->request->query('limit');
		}
		if (is_numeric($this->request->query('offset')))
		{
			   $offset = $this->request->query('offset');
		}
    	// this is a trick to convert 'false' to falsy (which would be true),
   		// 'true' to true, and an unset param to false
		$include_header = json_decode($this->request->query('include_header')) == true ? 1 : 0;

		// Construct console command input
		$input = new ArrayInput(array(
			'action' => 'export',
			'--limit' => $limit,
			'--offset' => $offset,
			'--job' => $job_id,
			'--include_header' => $include_header,
		 ), $command->getDefinition());

		// Create Output Buffer
		$output = new BufferedOutput();


		// Run the command
		$command->run($input, $output);

		// Retrieve the results of rhe export
		// which should be a json formatted string
		// containing information aboutt he file generated and
		// saved by the exporter
		$file_details = json_decode($output->fetch());

		$this->_response_payload = [
			'results' => $file_details,
		];
	}
}
