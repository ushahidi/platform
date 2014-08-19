<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\PostValue;
use Ushahidi\Entity\PostValueRepository;
use Ushahidi\Entity\GetValuesForPostRepository;

abstract class Ushahidi_Repository_PostValue extends Ushahidi_Repository implements PostValueRepository, GetValuesForPostRepository
{

	// Ushahidi_Repository
	protected function getEntity(Array $data = null)
	{
		return new PostValue($data);
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = parent::selectQuery($where);

		// Select 'key' too
		$query->select(
				$this->getTable().'.*',
				'form_attributes.key',
				'form_attributes.cardinality'
			)
			->join('form_attributes')->on('form_attribute_id', '=', 'form_attributes.id');

		return $query;
	}

	// PostValueRepository
	public function get($id)
	{
		return new PostValue($this->selectOne(compact('id')));
	}

	// GetValuesForPostRepository
	public function getAllForPost($post_id)
	{
		$query = $this->selectQuery(compact('post_id'));

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// PostValueRepository
	public function getValueQuery($form_attribute_id, $match)
	{
		return $this->selectQuery(compact('form_attribute_id'))
			->where('value', 'LIKE', "%$match%");
	}

}
