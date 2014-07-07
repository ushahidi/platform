<?php

/**
 * Ushahidi Platform Admin Create Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Media;

interface CreateMediaRepository
{
	/**
	 * @param  Array   $file  [name, type, size, tmp_name, error]
	 * @param  String  $caption (optional)
	 * @param  String  $user_id (optional)
	 */
	public function createMedia(Array $file, $caption = null, $user_id = null);

	/**
	 * @return  int
	 */
	public function getCreatedMediaId();

	/**
	 * @return  int
	 */
	public function getCreatedMediaTimestamp();

	/**
	 * @return Ushahidi\Entity\Media
	 */
	public function getCreatedMedia();
}


