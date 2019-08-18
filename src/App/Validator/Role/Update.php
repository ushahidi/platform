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

use Ushahidi\App\Validator\LegacyValidator;
use Ushahidi\Core\Entity\PermissionRepository;
use Ushahidi\App\Facades\Features;

class Update extends LegacyValidator
{
    protected $permission_repo;
    protected $feature_enabled;
    protected $default_error_source = 'role';

    public function __construct(PermissionRepository $permission_repo)
    {
        $this->permission_repo = $permission_repo;
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
            'name' => [
                [[$this, 'checkRolesEnabled'], [':validation']],
            ],
        ];
    }

    public function checkRolesEnabled(\Kohana\Validation\Validation $validation)
    {
        if (!Features::isEnabled('roles')) {
            $validation->error('name', 'rolesNotEnabled');
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
