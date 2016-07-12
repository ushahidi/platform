<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Update Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Traits\UserContext;

class Ushahidi_Validator_User_Update extends Validator
{
	use UserContext;

	protected $default_error_source = 'user';
	protected $repo;
	protected $role_repo;
	protected $valid;

	public function __construct(UserRepository $repo, RoleRepository $role_repo)
	{
		$this->repo = $repo;
		$this->role_repo = $role_repo;
	}

	protected function getRules()
	{
		return [
			'email' => [
				['email', [':value', TRUE]],
				['max_length', [':value', 150]],
				[[$this->repo, 'isUniqueEmail'], [':value']],
			],
			'realname' => [
				['max_length', [':value', 150]],
			],
			'role' => [
				[[$this->role_repo, 'exists'], [':value']],
				[[$this, 'checkAdminRoleLimit'], [':validation', ':value']]
			],
			'password' => [
				['min_length', [':value', 7]],
				['max_length', [':value', 72]],
			],
		];
	}

	public function checkAdminRoleLimit (Validation $validation, $role)
	{
		$config = \Kohana::$config->load('features.limits');

		if ($config['admin_users'] !== TRUE && $role == 'admin') {

			$total = $this->repo->getTotalCount(['role' => 'admin']);

			if ($total >= $config['admin_users']) {
				$validation->error('role', 'adminUserLimitReached');
			}
		}
	}

}
