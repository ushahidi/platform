<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Form;
use Ushahidi\Core\Entity\FormRepository;

class Ushahidi_Repository_Form extends Ushahidi_Repository implements
	FormRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'forms';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new Form($data);
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->parent) {
			$query->where('parent_id', '=', $search->parent);
		}

		if ($search->q) {
			// Form text searching
			$query->where('name', 'LIKE', "%{$search->q}%");
		}
	}

	// CreateRepository
	public function create(Data $data)
	{
		$record = array_filter($data->asArray());
		$record['created'] = time();

		// todo ensure default group is created

		return $this->executeInsert($record);
	}

	// UpdateRepository
	public function update($id, Data $input)
	{
		$update = $input->asArray();
		$update['updated'] = time();

		return $this->executeUpdate(compact('id'), $update);
	}

	// FormRepository
	public function doesFormExist($id)
	{
		return (bool) $this->selectCount(compact('id'));
	}
}
