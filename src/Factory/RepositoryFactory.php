<?php

/**
 * Ushahidi Platform Factory for Repositories
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Factory;

class RepositoryFactory
{
	// Array of repositories, mapped by resource:
	//
	//     $map = [
	//         'widgets' => $di->lazyNew('Namespace\To\WidgetRepository'),
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
	 * Gets a repository from the map by resource.
	 * @param  String $resource
	 * @return Ushahidi\Repository
	 */
	public function get($resource)
	{
		$factory = $this->map[$resource];
		return $factory();
	}
}
