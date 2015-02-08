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
use Ushahidi\Core\Usecase\User\UpdateUserData;
use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Traits\UserContext;

class Ushahidi_Validator_User_Update extends Validator
{
	use UserContext;

	protected $repo;
	protected $role;
	protected $valid;

	public function __construct(UserRepository $repo, RoleRepository $role)
	{
		$this->repo = $repo;
		$this->role = $role;
	}

	protected function getRules()
	{
		return [
			'email' => [
				['email'],
				[[$this->repo, 'isUniqueEmail'], [':value']],
			],
			'realname' => [
				['max_length', [':value', 150]],
			],
			'username' => [
				['regex', [':value', '/^[a-z][a-z0-9._-]+[a-z0-9]$/i']],
				[[$this->repo, 'isUniqueUsername'], [':value']],
			],
			'role' => [
				[[$this->role, 'doesRoleExist'], [':value']],
			],
			'password' => [
				['min_length', [':value', 7]],
				['max_length', [':value', 72]],
			],
		];
	}
}
