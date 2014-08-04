<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Authenticator
 *
 * Implemented using A1
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity;
use Ushahidi\Tool\Authorizer;
use Ushahidi\Exception\AuthorizerException;

class Ushahidi_Authorizer implements Authorizer
{
	protected $acl;
	protected $proxy_factory;

	public function __construct($acl, $proxy_factory)
	{
		$this->acl = $acl;
		$this->proxy_factory = $proxy_factory;
	}

	public function isAllowed(Entity $entity, $privilege, $user = FALSE)
	{
		$proxy_factory = $this->proxy_factory;
		$resource = $proxy_factory($entity);

		if ($user)
		{
			if (! $this->acl->is_allowed($user, $resource, $privilege, FALSE))
			{
				// @todo include entity id in error message
				throw new AuthorizerException('Not allowed to "' . $privilege .'" on "' . $resource->get_resource_id() .'"');
			}
		}
		else
		{
			// Fallback to checking with the authenticated user
			// This only works if the user has a session cookie, not with an API token
			if (!$this->acl->allowed($resource, $privilege, FALSE))
			{
				throw new AuthorizerException('Not allowed to "' . $privilege .'" on "' . $resource .'"');
			}
		}

		return TRUE;
	}
}

