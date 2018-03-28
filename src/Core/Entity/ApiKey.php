<?php

/**
 * Ushahidi ApiKey Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class ApiKey extends StaticEntity
{
	protected $id;
	protected $api_key;
	protected $created;
	protected $updated;


	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'            	=> 'int',
			'api_key'					=> 'string',
			'created'       	=> 'int',
			'updated'       	=> 'int',
		];
	}

	// Entity
	public function getResource()
	{
		return 'apikey';
	}
}
