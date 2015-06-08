<?php

/**
 * Ushahidi Saved Search
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

class SavedSearch extends Set
{
	protected $filter;

	// DataTransformer
	protected function getDefinition()
	{
		return parent::getDefinition() + [
			'filter'       => '*json',
		];
	}

	// Entity
	public function getResource()
	{
		return 'savedsearches';
	}
}
