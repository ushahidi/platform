<?php

/**
 * Ushahidi Platform Create Layer Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Layer;

interface CreateLayerRepository
{
	/**
	 * @param  String $layer
	 * @return Ushahidi\Entity\Layer
	 */
	public function createLayer(Array $input);
}
