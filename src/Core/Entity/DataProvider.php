<?php

/**
 * Ushahidi Data Provider Entity
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

class DataProvider extends StaticEntity implements Permissionable
{
	// Permissions
	use ManageSettings;

	protected $id;
	protected $name;
	protected $version;
	protected $services;
	protected $links;
	protected $options;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'       => 'string',
			'name'     => 'string',
			'version'  => 'float',
			'services' => 'array',
			'links'    => 'array',
			'options'  => 'array',
		];
	}

	// Entity
	public function getResource()
	{
		return 'dataprovider';
	}
}
