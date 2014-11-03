<?php

/**
 * Ushahidi Platform Delete Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

interface DeleteRepository
{
	/**
	 * @param  Integer $id
	 * @return Entity
	 */
	public function get($id);

	/**
	 * @param  Integer $id
	 * @return void
	 */
	public function delete($id);
}
