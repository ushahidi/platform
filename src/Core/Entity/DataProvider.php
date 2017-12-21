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

class DataProvider extends StaticEntity
{
	protected $id;
	protected $name;
	protected $services;
	protected $options;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'       => 'string',
			'name'     => 'string',
			'services' => 'array',
			'options'  => 'array',
		];
	}

	// Entity
	public function getResource()
	{
		return 'dataprovider';
	}
}
