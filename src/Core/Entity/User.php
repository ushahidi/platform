<?php

/**
 * Ushahidi Platform User Entity
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

class User extends StaticEntity implements Permissionable
{
	// Permissions
	use ManageSettings;

	protected $id;
	protected $email;
	protected $realname;
	protected $password;
	protected $logins;
	protected $failed_attempts;
	protected $last_login;
	protected $last_attempt;
	protected $created;
	protected $updated;
	protected $role;
	protected $permissions;

	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id'              => 'int',
			'email'           => '*email',
			'realname'        => 'string',
			'password'        => 'string',
			'logins'          => 'int',
			'failed_attempts' => 'int',
			'last_login'      => 'int',
			'last_attempt'    => 'int',
			'created'         => 'int',
			'updated'         => 'int',
			'role'            => 'string',
			'permissions'     => 'array',
		];
	}

	// Entity
	public function getResource()
	{
		return 'users';
	}
}
