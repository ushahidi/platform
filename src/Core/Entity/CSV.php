<?php

/**
 * Ushahidi CSV Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class CSV extends StaticEntity
{
	protected $id;
	protected $columns;
	protected $maps_to;
	protected $fixed;
	protected $filename;
	protected $mime;
	protected $size;
	protected $created;
	protected $updated;
	protected $completed;

	// DataTransformer
	public function getDefinition()
	{
		return [
			'id'           => 'int',
			'columns'      => '*json',
			'maps_to'      => '*json',
			'fixed'        => '*json',
			'filename'     => 'string',
			'mime'         => 'string',
			'size'         => 'int',
			'created'      => 'int',
			'updated'      => 'int',
			'completed'    => 'bool',
		];
	}

	// Entity
	public function getResource()
	{
		return 'csv';
	}
}
