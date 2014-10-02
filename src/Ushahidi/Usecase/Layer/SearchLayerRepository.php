<?php

/**
 * Repository for Searching Layers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Layer;

interface SearchLayerRepository
{
	/**
	 * @param  Ushahidi\Usecase\Layer\SearchLayerData $data
	 * @return [Ushahidi\Entity\Layer, ...]
	 */
	public function search(SearchLayerData $data);
}
