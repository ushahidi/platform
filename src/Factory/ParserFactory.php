<?php

/**
 * Ushahidi Platform Factory for Parsers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Factory;

class ParserFactory
{
	// Array of parsers, mapped by resource and action:
	//
	//     $map['widgets'] = [
	//         'create' => $di->lazyNew('Namespace\To\Widget\CreateParser'),
	//         'update' => $di->lazyNew('Namespace\To\Widget\UpdateParser'),
	//         ...
	//     ]
	//
	// Actions correspond with usecases, resources with entity types.
	protected $map = [];

	/**
	 * @param  Array $map
	 */
	public function __construct(Array $map)
	{
		$this->map = $map;
	}

	/**
	 * Gets a parser from the map by resource and action.
	 * @param  String $resource
	 * @param  String $action
	 * @return Ushahidi\Core\Tool\Parser
	 */
	public function get($resource, $action)
	{
		if (empty($this->map[$resource][$action])) {
			throw new \Exception(sprintf('Parser %s.%s is not defined', $resource, $action));
		}
		$factory = $this->map[$resource][$action];
		return $factory();
	}
}
