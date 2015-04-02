<?php

/**
 * Ushahidi Platform Resource Map trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport;

trait ResourceMapTrait
{

	protected $resourceMap;

	public function setResourceMap(ResourceMap $resourceMap)
	{
		$this->resourceMap = $resourceMap;
	}

}
