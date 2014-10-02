<?php

/**
 * Ushahidi Platform Update Layer Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Layer;

interface UpdateLayerRepository
{
	// LayerRepository
	public function get($id);

	/**
	 * @param  Integer $id
	 * @param  Array   $update
	 * @return Ushahidi\Entity\Layer
	 */
	public function updateLayer($id, Array $update);
}
