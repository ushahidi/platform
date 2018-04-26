<?php

/**
 * Ushahidi Role Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Role;

use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\PermissionRepository;

class Update extends Validator
{	
	protected $permission_repo;
	protected $feature;
	protected $default_error_source = 'role';

	public function __construct(PermissionRepository $permission_repo, array $feature)
	{
		$this->permission_repo = $permission_repo;
		$this->feature = $feature;
	}

	protected function getRules()
	{
		return [
			'id' => [
				['numeric'],
			],
			'permissions' => [
				[[$this, 'checkPermissions'], [':validation', ':value']],
			],
		];
	}

	public function checkRolesEnabled(\Kohana\Validation\Validation $validation, $permissions)
	{
		if ($this->feature['enabled']) {
			$validation->error('role', 'rolesNotEnabled');
			return;
		}
		return;
	}

	public function checkPermissions(\Kohana\Validation\Validation $validation, $permissions)
	{
		if (!$permissions) {
			return;
		}

		foreach ($permissions as $permission) {
			if (!$this->permission_repo->exists($permission)) {
				$validation->error('permissions', 'permissionDoesNotExist', [$permission]);
				return;
			}
		}
	}
}
