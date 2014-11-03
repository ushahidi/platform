<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Layer Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Layer;

class Ushahidi_Repository_Layer extends Ushahidi_Repository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'layers';
	}

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		// Decode options into an array
		$data['options'] = json_decode($data['options'], TRUE);

		return new Layer($data);
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->active !== null) {
			$query->where('active', '=', $search->active);
		}

		if ($search->type) {
			$query->where('type', '=', $search->type);
		}
	}

	// CreateRepository
	public function create(Data $input)
	{
		$record = $input->asArray();

		$record['created'] = time();
		$record['options'] = json_encode($record['options']);

		return $this->executeInsert($record);
	}

	// UpdateRepository
	public function update($id, Data $input)
	{
		$update = $input->asArray();

		$update['updated'] = time();
		if (isset($update['options']))
		{
			$update['options'] = json_encode($update['options']);
		}

		return $this->executeUpdate(compact('id'), $update);
	}
}
