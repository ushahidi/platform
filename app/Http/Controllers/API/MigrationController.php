<?php

namespace Ushahidi\App\Http\Controllers\API;

use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;

/**
 * Ushahidi API Migration Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class MigrationController extends Controller
{

	public function index(Request $request, $command = 'status')
	{
		$user = service('session')->getUser();

		if ('admin' != $user->role) {
			throw new HTTP_Exception_403('Must be an admin to access this service');
		}

		$commands = [
		    'status'   => 'getStatus',
		    'rollback' => 'getRollback',
		    ];

		// add return status if invalid command is selected

		if (!array_key_exists($command, $commands)) {
			$command = 'status';
		}

		$phinx_config = [
			'configuration' => base_path('phinx.php'),
			'parser' => 'php',
		];

		$phinx_app = new PhinxApplication();

		$phinx_wrapper = new TextWrapper($phinx_app, $phinx_config);

		$migration_results = call_user_func([$phinx_wrapper, $commands[$command]], 'ushahidi', null);
		$error  = $phinx_wrapper->getExitCode() > 0;

		return response()->json([
			'results'	=> explode("\n", $migration_results, -1),
		]);
	}

	public function migrate(Request $request)
	{
		return $this->index($request, 'migrate');
	}

	public function rollback(Request $request)
	{
		return $this->index($request, 'rollback');
	}

	public function status(Request $request)
	{
		return $this->index($request, 'status');
	}
}
