<?php

/**
 * Ushahidi Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface MediaRepository
{
	/**
	 * @param  Interger $id
	 * @return Ushahidi\Entity\Media
	 */
	public function get($id);

	/**
	 * @param  Ushahidi\Entity\MediaSearchData $data
	 * @param  Array $params [limit, offset, orderby, order]
	 * @return [Ushahidi\Entity\Media, ...]
	 */
	public function search(MediaSearchData $data, Array $params = null);
}
