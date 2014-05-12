<?php

/**
 * Ushahidi Platform User Login Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\User;

use Ushahidi\Entity\User;
use Ushahidi\Entity\UserRepository;
use Ushahidi\Tool\Validator;
use Ushahidi\Tool\PasswordAuthenticator;
use Ushahidi\Exception\Login as LoginException;

class Login
{
	private $repo;
	private $valid;
	private $auth;

	public function __construct(UserRepository $repo, Validator $valid, PasswordAuthenticator $auth)
	{
		$this->repo = $repo;
		$this->valid = $valid;
		$this->auth = $auth;
	}

	public function interact(User $user)
	{
		$this->valid->check($user);

		// Password is plaintext at this point, we will check after locating the user
		$password = $user->password;

		// Attempt to load the member user
		$member = $this->repo->getByUsername($user->username);

		// TODO: handle the other bits of A1, like rehashing and brute force checks
		$this->auth->checkPassword($password, $member->password);

		return $member->id;
	}
}


