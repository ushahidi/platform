<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi User Create Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\RoleRepository;

class Ushahidi_Validator_User_Create extends Validator
{
	protected $repo;
	protected $role_repo;

	protected $default_error_source = 'user';

	public function __construct(UserRepository $repo, RoleRepository $role_repo)
	{
		$this->repo = $repo;
		$this->role_repo = $role_repo;
	}

	protected function getRules()
	{
		return [
			'email' => [
				['not_empty'],
				['max_length', [':value', 150]],
				['email'],
				[[$this->repo, 'isUniqueEmail'], [':value']],
			],
			'realname' => [
				['max_length', [':value', 150]]
			],
			'username' => [
				['not_empty'],
				['max_length', [':value', 50]],
				[[$this->repo, 'isUniqueUsername'], [':value']],
				['regex', [':value', '/^[a-z][a-z0-9._-]+[a-z0-9]$/i']],
			],
			'password' => [
				['not_empty'],
				['min_length', [':value', 7]],
				['max_length', [':value', 72]]
			],
			'role' => [
				[[$this->role_repo, 'exists'], [':value']]
			],
		];
	}
}
