<?php

/**
 * Repository for Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface ConfigRepository
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

	/**
	 * @param  string $group
	 * @return \Ushahidi\Entity\Config
	 */
	public function get($group);

	/**
	 * @param  string $group
	 * @param  string $key
	 * @param  mixed  $value
	 * @return \Ushahidi\Entity\Config
	 */
	public function set($group, $key, $value);
}
