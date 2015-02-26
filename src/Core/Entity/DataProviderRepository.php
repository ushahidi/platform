<?php

/**
 * Repository for Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;

interface DataProviderRepository extends
	EntityGet
{
	/**
	 * Get all data providers.
	 * @param  Boolean $enabled only return providers that are enabled
	 * @return Array [Ushahidi\Core\Entity\DataProvider, ...]
	 */
	public function all($enabled = false);
}
