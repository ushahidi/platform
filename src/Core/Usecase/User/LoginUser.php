<?php

/**
 * Ushahidi Platform User Login Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\PasswordAuthenticator;
use Ushahidi\Core\Usecase\ReadRepository;
use Ushahidi\Core\Usecase\ReadUsecase;

use BehEh\Flaps\Flaps;
use BehEh\Flaps\Throttling\LeakyBucketStrategy;
use BehEh\Flaps\Throttling\ViolateAlwaysStrategy;
use BehEh\Flaps\Violation\ThrottlingViolationException;

class LoginUser extends ReadUsecase
{
	/**
	 * @var Authenticator
	 */
	protected $authenticator;

	/**
	 * @var BehEh\Flaps\Flap
	 */
	protected $rateLimiter;

	/**
	 * @var BehEh\Flaps\Throttling\LeakyBucketStrategy
	 */
	protected $throttlingStrategy;

	/**
	 * @param  Authenticator $authenticator
	 * @return void
	 */
	public function setAuthenticator(PasswordAuthenticator $authenticator)
	{
		$this->authenticator = $authenticator;
		return $this;
	}

	/**
	 * @param Flaps $rateLimiter
	 */
	public function setRateLimiter(Flaps $rateLimiter)
	{
		// Create new BehEh\Flaps\Flap called 'login'
		$this->rateLimiter = $rateLimiter->__get('login');
	}

	/**
	 * @param LeakyBucketStrategy $throttlingStrategy
	 */
	public function setThrottlingStrategy(LeakyBucketStrategy $throttlingStrategy)
	{
		$this->throttlingStrategy = $throttlingStrategy;
	}

	// Usecase
	public function interact()
	{
		// Fetch the entity, using provided identifiers...
		$entity = $this->getEntity();

		// Rate limit login attempts
		$this->rateLimiter->pushThrottlingStrategy($this->throttlingStrategy);
		$this->rateLimiter->limit($entity->email);

		// ... verify that the password matches
		// @todo: handle the other bits of A1, like rehashing and brute force checks
		$this->authenticator->checkPassword($this->getRequiredIdentifier('password'), $entity->password);

		// ... and return the formatted result.
		return $this->formatter->__invoke($entity);
	}

	// ReadUsecase
	protected function getEntity()
	{
		// Make sure the repository has then methods necessary.
		$this->verifyUserRepository($this->repo);

		// Entity will be loaded using the provided email
		$email = $this->getRequiredIdentifier('email');

		// ... attempt to load the entity
		$entity = $this->repo->getByEmail($email);

		// ... and verify that the entity was actually loaded
		$this->verifyEntityLoaded($entity, compact('email'));

		// ... then return it
		return $entity;
	}

	/**
	 * Verify that the given repository is a the correct type.
	 * (PHP is weird about overloaded type hinting.)
	 * @return UserRepository
	 */
	private function verifyUserRepository(UserRepository $repo)
	{
		return true;
	}
}
