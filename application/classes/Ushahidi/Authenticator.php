<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Password Authenticator
 *
 * Implemented using A1
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity;
use Ushahidi\Tool\Authenticator;
use Ushahidi\Exception\Authenticator as AuthenticatorException;;

class Ushahidi_Authenticator implements Authenticator
{
	private $a2;

	public function __construct()
	{
		$this->a2 = A2::instance();
	}

	public function isAllowed($resource, $privilege)
	{
		if ($resource instanceof Entity)
		{
			$resource = $resource->getResource();
		}

		if (!$this->a2->allowed($resource, $privilege, FALSE))
		{
			throw new AuthenticatorException('Not allowed to "' . $privilege .'" on "' . $resource .'"');
		}

		return TRUE;
	}
}

