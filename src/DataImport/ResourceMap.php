<?php

/**
 * Ushahidi Platform Resource Maps
 *
 * Track source-to-destination mapping during data import
 * ie. Categories on a 2.x source to tags on the destination
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport;

use Ushahidi\Core\Traits\GetSet;

class ResourceMap
{
	use GetSet;

	public function getMappedId($resource, $originalId)
	{
		$map = $this->get($resource, []);
		return isset($map[$originalId]) ? $map[$originalId] : false;
	}

}
