<?php

/**
 * Ushahidi Platform Factory for Filesystem Adapters
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Factory;

class FilesystemAdapterFactory
{
	// Array of adapters, mapped by filesystem type:
	//
	//     $map = [
	//         'adapters' => $di->lazyNew('Namespace\To\FilesystemAdapterType'),
	//         ...
	//     ]
	//
	// Resource names correspond with adapter types.
	protected $map = [];

	/**
	 * @param  Array $map
	 */
	public function __construct(Array $map)
	{
		$this->map = $map;
	}

	/**
	 * Gets an adapter from the map by type.
	 * @param  String $type
	 * @return Ushahidi\Repository
	 */
	public function get($type)
	{
		$adapter = $this->map[$type];

		return $adapter->getAdapter();
	}
}
