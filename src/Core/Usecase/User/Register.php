<?php

/**
 * Ushahidi Platform User Register Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Exception\ValidatorException;

class Register
{
	private $repo;
	private $valid;

	public function __construct(RegisterRepository $repo, Validator $valid)
	{
		$this->repo = $repo;
		$this->valid = $valid;
	}

	public function interact(RegisterData $input)
	{
		if (!$this->valid->check($input)) {
			throw new ValidatorException("Failed to validate user registration", $this->valid->errors());
		}

		$userid = $this->repo->register(
			$input->email,
			$input->username,
			$input->password
		);
		return $userid;
	}
}
