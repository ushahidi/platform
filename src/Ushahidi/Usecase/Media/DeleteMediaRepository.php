<?php

/**
 * Ushahidi Platform Admin Delete Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

interface DeleteMediaRepository
{
	/**
	 * @param  Integer $id
	 * @return Ushahidi\Entity\Media
	 */
	public function get($id);

	/**
	 * @param  Integer $id
	 * @param  String  $user_id (optional)
	 */
	public function deleteMedia($id, $user_id = null);
}
