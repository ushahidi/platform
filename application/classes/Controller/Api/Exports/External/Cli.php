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
		$limit = $this->request->param('limit', 0);
		$offset = $this->request->param('offset', 0);
		$add_header = true;

		$job_id = $this->request->param('id');

		// Construct console command input
		$input = new ArrayInput(array(
			'action' => 'export',
			'--limit' => $limit,
			'--offset' => $offset,
			'--job' => $job_id,
			'--add-header' => $add_header,
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
