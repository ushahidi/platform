<?php

/**
 * Ushahidi Config Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\DynamicEntity;
use Ushahidi\Core\Traits\Permissions\ManageSettings;
use Ushahidi\Core\Tool\Permissions\Permissionable;

class Config extends DynamicEntity implements Permissionable
{
	// Permissions
	use ManageSettings;
	
	// DataTransformer
	protected function getDefinition()
	{
		return ['id' => 'string'];
	}

	// Entity
	public function getResource()
	{
		return 'config';
	}

	// StatefulData
	public function getImmutable()
	{
		return array_merge(parent::getImmutable(), ['allowed_privileges']);
	}
}
