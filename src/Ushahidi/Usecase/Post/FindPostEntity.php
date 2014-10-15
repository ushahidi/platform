<?php

/**
 * Ushahidi Find Post Entity Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Post;

use Ushahidi\Data;

trait FindPostEntity
{
	/**
	 * Find post entity based on read data
	 * @param  Data    $input
	 * @return Entity\Post
	 */
	protected function getEntity(Data $input)
	{
		if ($input->parent_id && $input->locale) {
			return $this->repo->getByLocale($input->locale, $input->parent_id);
		} else {
			// Load post by id and parent id, because if its a revision or update
			// we should only return revision for the particular parent post
			return $this->repo->getByIdAndParent($input->id, $input->parent_id);
		}
	}
}
