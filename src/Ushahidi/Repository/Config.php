<?php

/**
 * Repository for Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Repository;

interface Config
{

	/**
	 * @return array
	 */
	public function groups();

	/**
	 * @param  string $group
	 * @return array
	 */
	public function all($group = null);

	/**
	 * @param  string $group
	 * @param  string $key
	 * @return mixed
	 */
	public function get($group, $key);

	/**
	 * @param  string $group
	 * @param  string $key
	 * @param  mixed  $value
	 * @return mixed
	 */
	public function set($group, $key, $value);

}
