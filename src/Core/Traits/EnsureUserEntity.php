<?php

/**
 * Ushahidi Ensure User Entity trait
 *
 * Gives objects one new method:
 * `ensureUserIsEntity($user)`
 *
 * This checks if `$user` is a User Entity and loads
 * an entity if its not.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\UserRepository;

trait EnsureUserEntity
{

	// It requires a `UserRepository` to load `User` entities and
	// check credentials such as a users role.
	protected $user_repo;

	// It defines a default constructor which accepts a `UserRepository`
	/**
	 * @param UserRepository $user_repo
	 */
	public function __construct(UserRepository $user_repo)
	{
		$this->user_repo = $user_repo;
	}

	/**
	 * Ensure user is a User Entity, or load it from the user repo
	 * @param  User|Int $user  User Entity or user id
	 * @return User
	 */
	protected function ensureUserIsEntity(&$user)
	{
		// Check if the user is an instance of `User`
		if (! $user instanceof User) {
			// If we only have a user id, we load the full entity.
			$user = $this->user_repo->get($user);
		}

		return $user;
	}
}
