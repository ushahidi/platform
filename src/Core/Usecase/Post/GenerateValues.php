<?php

/**
 * Ushahidi Generate Values Trait
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

use Ushahidi\Core\Data;
use Ushahidi\Core\Usecase\Post\PostData;

trait GenerateValues
{
	protected function fillGeneratedValues(Data $input)
	{
		$data = $input->asArray();

		// Auto generate a slug value
		if (empty($data['slug']) && !empty($data['title'])) {
			// Based on Kohana URL::title()
			// replace whitespace and multiple separator chars with a single separator
			$slug = preg_replace(
				'/[\-\s]+/u',
				'-',
				// Remove all characters that are not the separator, letters, numbers, or whitespace
				preg_replace(
					'/[^\-\pL\pN\s]+/u',
					'',
					// UTF-8 safe strtolower()
					mb_strtolower($data['title'], 'utf-8')
				)
			);


			// If the slug is already taken, add a random id
			if (! $this->repo->isSlugAvailable($slug)) {
				$slug = $slug . '-' . uniqid();
			}

			$data['slug'] = $slug;
		}

		// If we have no user id or author info, and we have a logged in user
		// set user_id = current user
		if (
			empty($data['user_id']) &&
			empty($data['author_email']) &&
			empty($data['author_realname']) &&
			$this->auth->getUserId()
		) {
			$data['user_id'] = $this->auth->getUserId();
		}

		// Populate type and parent_id
		$data['type'] = $this->type;
		$data['parent_id'] = $this->parent_id;

		return new PostData($data);
	}
}
