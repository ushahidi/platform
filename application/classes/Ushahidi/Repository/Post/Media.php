<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

//use Ushahidi\Core\Entity\PostValue;
//use Ushahidi\Core\Entity\PostValueRepository;

class Ushahidi_Repository_Post_Media extends Ushahidi_Repository_Post_Value
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'post_media';
	}

	// ValuesForPostRepository
	public function getAllForPost($post_id, Array $include_attributes = [], Array $exclude_stages = [], $restricted = false)
	{
		$query = $this->selectQuery(compact('post_id'));

		if ($include_attributes) {
			$query->where('form_attributes.key', 'IN', $include_attributes);
		}

		if ($restricted) {
			$query->where('form_attributes.response_private', '!=', '1');
			if ($exclude_stages) {
				$query->where('form_attributes.form_stage_id', 'NOT IN', $exclude_stages);
			}
		}
		//$query->join('media')->on('form_attribute_id', '=', 'form_attributes.id');
		$results = $query->execute($this->db);
		return $this->getCollection($results->as_array());
	}


	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = Ushahidi_Repository::selectQuery($where);

		// Select 'key' too
		$query->select(
			$this->getTable().'.*',
			'media.o_filename',
			'form_attributes.key',
			'form_attributes.form_stage_id',
			'form_attributes.response_private'
		)
			->join('media')->on('form_attribute_id', '=', 'form_attribute_id')
			->join('form_attributes')->on('form_attribute_id', '=', 'form_attributes.id');

		return $query;
	}
}
