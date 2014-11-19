<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Update Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Usecase\User\UpdateUserData;
use Ushahidi\Core\Entity\RoleRepository;

class Ushahidi_Validator_User_Update implements Validator
{	
	protected $repo;
	protected $valid;
	protected $user;
	protected $role;

	public function __construct(UserRepository $repo, User $user, RoleRepository $role)
	{
		$this->repo = $repo;
		$this->user = $user;
		$this->role = $role;
	}

	public function check(Data $input)
	{
		$this->valid = Validation::factory($input->asArray());
		$this->valid
			->rules('email', [
								['email'],
								[[$this->repo, 'isUniqueEmail'], [':value']]
							]
					)
			->rules('realname', [
									['max_length', [':value', 150]]
								]
					)
			->rules('username', [
									[[$this->repo, 'isUniqueUsername'], [':value']],
									['regex', [':value', '/^[a-z][a-z0-9._-]+[a-z0-9]$/i']],
								]
					)
			->rules('role', [
								[[$this, 'isUserSelf'], [$input]],
								[[$this->role, 'doesRoleExist'], [':value']]
							]
					)
			->rules('password', [
									['min_length', [':value', 7]],
									['max_length', [':value', 72]]
								]
					);

		return $this->valid->check();
	}

	/*
	 * User cannot change his own role
	 */
	public function isUserSelf(Data $input)
	{
		return !($input->id === $this->user->id);
	}

	public function errors($from = 'user')
	{
		return $this->valid->errors($from);
	}
}
