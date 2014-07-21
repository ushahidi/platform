<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Geometry Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\PostValue;
use Ushahidi\Entity\PostValueRepository;

class Ushahidi_Repository_PostGeometry extends Ushahidi_Repository_PostValue
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'post_geometry';
	}

	// Override selectQuery to fetch 'value' from db as text
	protected function selectQuery(Array $where = [])
	{
		$query = DB::select(
				'id',
				'post_id',
				'form_attribute_id',
				// Fetch AsText(value) aliased to value
				[DB::expr('AsText(value)'), 'value'],
				'created'
			)
			->from($this->getTable());
		foreach ($where as $column => $value)
		{
			$query->where($column, '=', $value);
		}
		return $query;
	}

}
