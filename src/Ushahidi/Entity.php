<?php

/**
 * Ushahidi Platform Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi;

interface Entity
{
	/**
	 * @param  array  values to be changed
	 * @return $this
	 */
	public function setData($data);

	/**
	 * @return array
	 **/
	public function asArray();
}
