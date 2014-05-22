<?php

/**
 * Ushahidi Platform User Register Use Case
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
		$this->valid->check($input);
		$userid = $this->repo->register(
			$input->email,
			$input->username,
			$input->password);
		return $userid;
	}
}
