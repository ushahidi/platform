<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Relation Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Repository_Post_Tags extends Ushahidi_Repository_Post_Value
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'posts_tags';
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = parent::selectQuery($where);

		// Select 'tag_id' as value too
		$query->select(
				['posts_tags.tag_id', 'value']
			);

		return $query;
	}

	// PostValueRepository
	public function getValueQuery($form_attribute_id, $match)
	{
		return $this->selectQuery(compact('form_attribute_id'))
			->where('tag_id', 'LIKE', "%$match%");
	}

	// UpdatePostValueRepository
	public function createValue($value, $form_attribute_id, $post_id)
	{
		$tag_id = $value;
		$input = compact('tag_id', 'form_attribute_id', 'post_id');
		$input['created'] = time();

		return $this->executeInsert($input);
	}

	// UpdatePostValueRepository
	public function updateValue($id, $value)
	{
		$update = ['tag_id' => $value];
		if ($id && $update)
		{
			$this->executeUpdate(compact('id'), $update);
		}
	}

}
