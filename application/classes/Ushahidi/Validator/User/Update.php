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

class Ushahidi_Validator_User_Update implements Validator
{	
	protected $repo;
	protected $role;
	protected $valid;

	public function __construct(UserRepository $repo, RoleRepository $role)
	{
		$this->repo = $repo;
		$this->role = $role;
	}

	public function check(Entity $entity)
	{
		$this->valid = Validation::factory($entity->getChanged())
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
								[[$this->role, 'doesRoleExist'], [':value']]
							]
					)
			->rules('password', [
									['min_length', [':value', 7]],
								]
					);

		return $this->valid->check();
	}

	public function errors($from = 'user')
	{
		return $this->valid->errors($from);
	}
}
