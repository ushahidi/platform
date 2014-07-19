<?php

/**
 * Ushahidi Platform Admin Delete Tag Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Tag;

interface DeleteTagRepository
{
	/**
	 * @param  Integer $id
	 */
	public function deleteTag($id);

	/**
	 * @return Ushahidi\Entity\Tag
	 */
	public function getDeletedTag();
}

