<?php

/**
 * Ushahidi Post
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Post extends StaticEntity
{
	protected $id;
	protected $parent_id;
	protected $form_id;
	protected $user_id;
	protected $type;
	protected $title;
	protected $slug;
	protected $content;
	protected $author_email;
	protected $author_realname;
	protected $status;
	protected $created;
	protected $updated;
	protected $locale;
	protected $values;
	protected $tags;

	// StatefulData
	protected function getDerived()
	{
		return [
			'slug'    => 'title',
			'form_id' => ['form', 'form.id'], /* alias */
			'user_id' => ['user', 'user.id'], /* alias */
		];
	}

	// DataTransformer
	protected function getDefinition()
	{
		$isList = function (Array $array) {
			return array_values($array) === $array;
		};
		$removeEmptyValues = function ($value) {
			if (is_object($value)) {
				return !empty($value->value);
			}
			if (is_array($value)) {
				return !empty($value['value']);
			}
			return false;
		};
		$transformValues = function ($values) use ($isList, $removeEmptyValues) {
			if (!is_array($values)) {
				return [];
			}
			foreach ($values as $key => $value) {
				if (is_array($value) && $isList($value)) {
					// Clean out empty values in correct structure:
					//
					//     [
					//         {hospital: 'Mercy General', id: 1},
					//         {diagnosis: null, id: 2}
					//     ]
					//
					$values[$key] = array_filter($value, $removeEmptyValues);
				} elseif (is_object($value)) {
					// Assume it is already a Value entity.
					$values[$key] = [$value];
				} else {
					// Handle simple and complex values:
					//
					//     {current_age: 46}
					//     {last_location: {lat: 33.53, lon: 112.91}}
					//
					$values[$key] = [['value' => $value]];
				}
			}
			return $values;
		};
		return [
			'id'              => 'int',
			'parent_id'       => 'int',
			'form'            => false, /* alias */
			'form_id'         => 'int',
			'user'            => false, /* alias */
			'user_id'         => 'int',
			'type'            => 'string',
			'title'           => 'string',
			'slug'            => '*slug',
			'content'         => 'string',
			'author_email'    => 'string', /* @todo email filter */
			'author_realname' => 'string', /* @todo redundent with user record */
			'status'          => 'string',
			'created'         => 'int',
			'updated'         => 'int',
			'locale'          => 'string',
			'values'          => $transformValues,
			'tags'            => 'array',
		];
	}

	// Entity
	public function getResource()
	{
		return 'posts';
	}
}
