<?php

/**
 * Ushahidi Platform Factory for Authorizers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Factory;

class AuthorizerFactory
{
	// Array of authorizers, mapped by resource:
	//
	//     $map = [
	//         'widgets' => $di->lazyNew('Namespace\To\WidgetAuthorizer'),
	//         ...
	//     ]
	//
	// Resource names correspond with entity types.
	protected $map = [];

	/**
	 * @param  Array $map
	 */
	public function __construct(Array $map)
	{
		$this->map = $map;
	}

	/**
	 * Gets an authorizer from the map by resource.
	 * @param  String $resource
	 * @return Ushahidi\Core\Tool\Authorizer
	 */
	public function get($resource)
	{
		$factory = $this->map[$resource];
		return $factory();
	}
}
