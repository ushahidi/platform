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
use Ushahidi\Exception\ValidatorException;

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

	public function interact(LoginData $input)
	{
		if (!$this->valid->check($input))
			throw new ValidatorException("Failed to validate login", $this->valid->errors());

		// Attempt to load the user
		$user = $this->repo->getByUsername($input->username);

		// TODO: handle the other bits of A1, like rehashing and brute force checks
		$this->auth->checkPassword($input->password, $user->password);

		return $user->id;
	}
}


