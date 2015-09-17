<?php

/**
 * Ushahidi Client Superpower Access Trait
 *
 * Gives objects one new method:
 * `hasSuperpowers(Client $client)`
 *
 * This checks if `$client` has superpowers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity\Client;

trait SuperpoweredAccess
{

	/**
	 * Check if the user has an Admin role
	 * @param  User    $user
	 * @return boolean
	 */
	protected function hasSuperpowers(Client $client)
	{
		return ($client->id && $client->superpowers);
	}
}
