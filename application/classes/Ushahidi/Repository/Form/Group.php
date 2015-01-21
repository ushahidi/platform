<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Group Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\FormGroup;
use Ushahidi\Core\Entity\FormGroupRepository;

class Ushahidi_Repository_Form_Group extends Ushahidi_Repository implements
	FormGroupRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'form_groups';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new FormGroup($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['form_id', 'label'];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->form_id) {
			$query->where('form_id', '=', $search->form_id);
		}

		if ($search->q) {
			// Form group text searching
			$query->where('label', 'LIKE', "%{$search->q}%");
		}
	}
}
