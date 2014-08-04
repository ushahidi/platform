<?php

/**
 * Ushahidi Platform Admin Create Tag Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Tag;

interface CreateTagRepository
{
	/**
	 * @param  String $tag
	 * @param  String $slug
	 * @param  String $description
	 * @param  String $type  one of: category, status
	 * @param  String $color
	 * @param  String $icon
	 * @param  String $role
	 */
	public function createTag($tag, $slug, $description, $type, $color = null, $icon = null, $role = null);

	/**
	 * @return  int
	 */
	public function getCreatedTagId();

	/**
	 * @return  int
	 */
	public function getCreatedTagTimestamp();

	/**
	 * @return Ushahidi\Entity\Tag
	 */
	public function getCreatedTag();
}

