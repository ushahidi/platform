<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Migration Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Migrate extends Ushahidi_Rest {

	protected $_action_map = array
	(
		Http_Request::GET    => 'get',
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return 'migrate';
	}

	protected function _is_auth_required()
	{
		return FALSE;
	}

	public function action_get_index_collection()
	{

		$db = service('db.config');
		$phinx_config = ['configuration' => realpath(APPPATH . '../application/phinx.php'),
			'parser' => 'php',
		];

		$phinx_app = new Phinx\Console\PhinxApplication();

		$phinx_wrapper = new Phinx\Wrapper\TextWrapper($phinx_app, $phinx_config);

		$migration_results = call_user_func([$phinx_wrapper, 'getMigrate'], 'ushahidi', null);
		$error  = $phinx_wrapper->getExitCode() > 0;

		$this->_response_payload = [
			'results'	=> explode("\n", $migration_results, -1),
		];
	}

}
