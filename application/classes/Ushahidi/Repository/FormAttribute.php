<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Attribute Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\FormAttribute;
use Ushahidi\Entity\FormAttributeRepository;

class Ushahidi_Repository_FormAttribute extends Ushahidi_Repository implements FormAttributeRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'form_attributes';
	}

	// Ushahidi_Repository
	protected function getEntity(Array $data = null)
	{
		return new FormAttribute($data);
	}

	// FormAttributeRepository
	public function get($id)
	{
		return new FormAttribute($this->selectOne(compact('id')));
	}

	// FormAttributeRepository
	public function getAll()
	{
		$query = $this->selectQuery();

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}
}
