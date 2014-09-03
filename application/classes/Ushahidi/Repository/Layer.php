<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Layer Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\Layer;
use Ushahidi\Entity\LayerRepository;
use Ushahidi\Entity\LayerSearchData;

class Ushahidi_Repository_Layer extends Ushahidi_Repository implements LayerRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'layers';
	}

	// Ushahidi_Repository
	protected function getEntity(Array $data = null)
	{
		// Decode options into an array
		$data['options'] = json_decode($data['options'], TRUE);

		return new Layer($data);
	}

	// LayerRepository
	public function get($id)
	{
		return $this->getEntity($this->selectOne(compact('id')));
	}

	// LayerRepository
	public function search(LayerSearchData $search, Array $params = null)
	{
		$where = array_filter(Arr::extract($search->asArray(), ['active', 'type']));

		if ($search->active !== NULL)
		{
			$where['active'] = $search->active;
		}

		// Start the query, removing empty values
		$query = $this->selectQuery($where);

		if (!empty($params['orderby'])) {
			$query->order_by($params['orderby'], Arr::get($params, 'order'));
		}
		if (!empty($params['offset'])) {
			$query->offset($params['offset']);
		}
		if (!empty($params['limit'])) {
			$query->limit($params['limit']);
		}

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

}
