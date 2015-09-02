<?php

/**
 * Ushahidi Notification Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PrivAccess;

class NotificationAuthorizer implements Authorizer
{
	// The access checks are run under the context of a specific user
	use UserContext;

	// To check whether the user has admin access
	use AdminAccess;

	// It uses `PrivAccess` to provide the `getAllowedPrivs` method.
	use PrivAccess;

	// Requires `ContactRepository` to load the notification contact
	protected $contact_repo;

	// Requires `ContactAuthorizer` to check if the notification is owned by the contact
	protected $contact_auth;

	public function __construct(ContactRepository $contact_repo, ContactAuthorizer $contact_auth)
	{
		$this->contact_repo = $contact_repo;
		$this->contact_auth = $contact_auth;
	}

	/* Authorizer */
	public function isAllowed(Entity $entity, $privilege)
	{
		// These checks are run within the user context.
		$user = $this->getUser();

		//Admin is allowed access to everything
		if ($this->isUserAdmin($user)) {
			return true;
		}

		$contact = $this->getContact($entity);

		// Check that the user also owns the contact
		// but only allow read, update and search
		if ($this->contact_auth->isAllowed($contact, $privilege)
			and in_array($privilege, ['read', 'update', 'search'])) {

			return true;
		}

		// Logged in users can subscribe to notifications
		if ($user->getId() and $privilege === 'create') {
			return true;
		}

		// If no other access checks succeed, we default to denying access
		return false;
	}

	protected function getContact(Entity $entity)
	{
		return $this->contact_repo->get($entity->contact_id);
	}
}
