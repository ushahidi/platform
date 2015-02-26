<?php

/**
 * Repository for Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\Entity\Repository\EntityGet;

interface ConfigRepository extends
	EntityGet
{
	/**
	 * @return array
	 */
	public function groups();

	/**
	 * @param  array $groups
	 * @return array
	 */
	public function all(array $groups = null);
}
