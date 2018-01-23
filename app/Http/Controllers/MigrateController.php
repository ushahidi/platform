<?php

namespace Ushahidi\App\Http\Controllers;

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

class MigrateController extends Controller
{

	public function migrate()
	{
		$phinx_config = [
			'configuration' => base_path('phinx.php'),
			'parser' => 'php',
		];

		$phinx_app = new PhinxApplication();

		$phinx_wrapper = new TextWrapper($phinx_app, $phinx_config);

		$migration_results = call_user_func([$phinx_wrapper, 'getMigrate'], 'ushahidi', null);
		$error  = $phinx_wrapper->getExitCode() > 0;

		return response()->json([
			'results'	=> explode("\n", $migration_results, -1),
		]);
	}
}
