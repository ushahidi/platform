<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Ushahidi_Repository_CSVPost extends Ushahidi_Repository_Post
{
	protected function getPostValues($id)
	{

		// Get all the values for the post. These are the EAV values.
		$values = $this->post_value_factory
			->proxy($this->include_value_types)
			->getAllForPost($id, $this->include_attributes, $this->exclude_stages, $this->restricted);

		$output = [];
		foreach ($values as $value) {
			if (empty($output[$value->key])) {
				$output[$value->key] = [];
			}
			if (is_array($value->value) && isset($value->value['o_filename'])) {
				$output[$value->key][] = $value->value['o_filename'];
			} else if ($value->value !== NULL) {
				$output[$value->key][] = $value->value;
			}
		}
		return $output;
	}
}
