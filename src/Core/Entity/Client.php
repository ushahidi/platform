<?php

/**
 * Ushahidi Platform Client Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class Client extends StaticEntity
{
	protected $id;
	protected $secret;
	protected $superpowers;
	protected $name;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'              => 'string',
			'secret'          => 'string',
			'superpowers'     => 'bool',
			'name'            => 'string',
		];
	}

	// Entity
	public function getResource()
	{
		return 'clients';
	}
}
