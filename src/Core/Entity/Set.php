<?php

/**
 * Ushahidi Set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Set extends StaticEntity
{
	protected $id;
	protected $user_id;
	protected $name;
	protected $url;
	protected $filter;
	protected $created;
	protected $updated;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'      => 'int',
			'user_id' => 'int',
			'name'    => 'string',
			'url'     => '*url',
			'filter'  => 'string',
			'created' => 'int',
			'updated' => 'int',
		];
	}

	// Entity
	public function getResource()
	{
		return 'sets';
	}
}
