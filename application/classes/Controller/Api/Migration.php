<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Migration Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Migration extends Ushahidi_Rest {

	protected $_action_map = array
	(
		Http_Request::GET     => 'get',
		Http_Request::POST    => 'post',
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return 'migrate';
	}

	public function action_get_index_collection($command = 'status')
	{

		$user = service('session.user');

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

		$db = service('db.config');
		$phinx_config = ['configuration' => realpath(APPPATH . '../application/phinx.php'),
			'parser' => 'php',
		];

		$phinx_app = new Phinx\Console\PhinxApplication();

		$phinx_wrapper = new Phinx\Wrapper\TextWrapper($phinx_app, $phinx_config);

		$migration_results = call_user_func([$phinx_wrapper, $commands[$command]], 'ushahidi', null);
		$error  = $phinx_wrapper->getExitCode() > 0;

		$this->_response_payload = [
			'results'	=> explode("\n", $migration_results, -1),
		];
	}

	public function action_post_migrate_collection()
	{
		$this->action_get_index_collection('migrate');
	}

	public function action_post_rollback_collection()
	{
		$this->action_get_index_collection('rollback');
	}

	public function action_get_status_collection()
	{
		$this->action_get_index_collection('status');
	}
}
