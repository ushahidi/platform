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
	public function getEntity(Array $data = null)
	{
		/**
		 * This value is added here so that we can manipulate it in the CSV getpostvalues and use either id or filename
		 * depending on the repository used
		 */
		$data['value'] = array('o_filename' => $data['o_filename'], 'id' =>$data['id']);
		return new \Ushahidi\Core\Entity\PostValueMedia($data);
	}
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'post_media';
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = Ushahidi_Repository::selectQuery($where);

		// Select 'key' too
		$query->select(
			$this->getTable().'.*',
			'media.o_filename',
			'media.id',
			'form_attributes.key',
			'form_attributes.form_stage_id',
			'form_attributes.response_private'
		)
			->join('media')->on('value', '=', 'media.id')
			->join('form_attributes')->on('form_attribute_id', '=', 'form_attributes.id');

		return $query;
	}
}