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
	 * @param  string $slug
	 * @return bool
	 */
	public function isSlugAvailable($slug);

	/**
	 * @param Array $input
	 */
	public function createTag(Array $input);

	/**
	 * @return  int
	 */
	public function getCreatedTagId();

	/**
	 * @return  int
	 */
	public function getCreatedTagTimestamp();
}

