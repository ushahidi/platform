<?php

/**
 * Ushahidi Authorizer Tool Trait
 *
 * Gives objects a method for storing an authorizer instance.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tool;

trait AuthorizerTrait
{
	/**
	 * @var Ushahidi\Tool\Authorizer
	 */
	protected $auth;

	/**
	 * @param  Ushahidi\Tool\Authorizer $auth
	 * @return void
	 */
	private function setAuthorizer(Authorizer $auth)
	{
		$this->auth = $auth;
	}
}
