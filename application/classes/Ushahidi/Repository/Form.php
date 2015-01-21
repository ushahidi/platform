<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Form;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\SearchData;

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

	// SearchRepository
	public function getSearchFields()
	{
		return ['parent', 'q' /* LIKE name */];
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
	public function create(Entity $entity)
	{
		// todo ensure default group is created
		return parent::create($entity->setState(['created' => time()]));
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		return parent::update($entity->setState(['updated' => time()]));
	}

	// FormRepository
	public function doesFormExist($id)
	{
		return (bool) $this->selectCount(compact('id'));
	}
}
