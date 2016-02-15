<?php

/**
 * Ushahidi Form
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;
use Ushahidi\Core\Traits\Permissions\ManageSettings;
use Ushahidi\Core\Tool\Permissions\Permissionable;

class Form extends StaticEntity implements Permissionable
{
	// Permissions
	use ManageSettings;

	protected $id;
	protected $parent_id;
	protected $name;
	protected $description;
	protected $type;
	protected $disabled;
	protected $created;
	protected $updated;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'          => 'int',
			'parent_id'   => 'int',
			'name'        => 'string',
			'description' => 'string',
			'type'        => 'string',
			'disabled'    => 'bool',
			'created'     => 'int',
			'updated'     => 'int',
		];
	}

	// Entity
	public function getResource()
	{
		return 'forms';
	}
}
