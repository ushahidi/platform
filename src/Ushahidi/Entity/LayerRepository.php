<?php

/**
 * Ushahidi Layer Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

interface LayerRepository
{
	/**
	 * @param  Integer $id
	 * @return Ushahidi\Entity\Layer
	 */
	public function get($id);

	/**
	 * @param  Ushahidi\Entity\LayerSearchData $data
	 * @param  Array $params [limit, offset, orderby, order]
	 * @return [Ushahidi\Entity\Layer, ...]
	 */
	public function search(LayerSearchData $data, Array $params = null);
}
