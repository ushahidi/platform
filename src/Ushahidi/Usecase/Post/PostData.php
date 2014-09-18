<?php

/**
 * Ushahidi Platform Post Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Usecase\Post;

use Ushahidi\Data;

class PostData extends Data
{
	public $id;
	public $form_id;
	public $user_id;
	public $author_email;
	public $author_realname;
	public $title;
	public $slug;
	public $content;
	public $status;
	public $locale;

	// @todo figure out if these should live elsewhere
	public $type;
	public $parent_id;

	public $values = [];
	public $tags = [];

	/**
	 * Compare with some existing data and get the delta between the two.
	 * Only values that were present in the input data will be returned!
	 * @param  Array  $compare  existing data
	 * @return Data
	 */
	public function getDifferent(Array $compare)
	{
		// Get the difference of current data and comparison. If not all properties
		// were defined in input, this will contain false positive (empty) values.
		// Exclude values and tags, since array_diff_assoc can't cope with arrays.
		$delta = $this->diff($this->asArray(), $compare, ['tags', 'values']);

		$delta['values'] = $this->diffValues($this->values, $compare['values']);
		$delta['tags']	 = $this->diffTags($this->tags, $compare['tags']);

		return new static($delta);
	}

	protected function diff($base, $compare, $excluding)
	{
		$base = array_diff_key($base, array_flip($excluding));
		$compare = array_diff_key($compare, array_flip($excluding));

		return array_diff_assoc($base, $compare);
	}

	protected function diffValues($base, $compare)
	{
		// @todo recursive diff on values
		// For now, just assume they're always updated
		return $base;
	}

	protected function diffTags($base, $compare)
	{
		// @todo recursive diff on tags
		// For now, just assume they're always updated
		return $base;
	}
}
