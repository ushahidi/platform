<?php

/**
 * Ushahidi User Update Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\User;

use Ushahidi\Core\Facade\Feature;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Contracts\Repository\Entity\RoleRepository;
use Ushahidi\Contracts\Repository\Entity\UserRepository;

class Update extends LegacyValidator
{
    use UserContext;

    protected $default_error_source = 'user';
    protected $repo;
    protected $role_repo;
    protected $valid;

    public function __construct(UserRepository $repo, RoleRepository $role_repo)
    {
        $this->repo = $repo;
        $this->role_repo = $role_repo;
    }

    protected function getRules()
    {
        return [
            'email' => [
                ['email', [':value', true]],
                ['max_length', [':value', 150]],
                [[$this->repo, 'isUniqueEmail'], [':value']],
            ],
            'realname' => [
                ['max_length', [':value', 150]],
            ],
            'role' => [
                [[$this->role_repo, 'exists'], [':value']],
                [[$this, 'checkAdminRoleLimit'], [':validation', ':value']]
            ],
            'password' => [
                ['min_length', [':value', 7]],
                ['max_length', [':value', 72]],
            ],
        ];
    }

    public function checkAdminRoleLimit(\Kohana\Validation\Validation $validation, $role)
    {
        $limit = Feature::getLimit('admin_users');
        if ($limit !== INF && $role == 'admin') {
            $total = $this->repo->getTotalCount(['role' => 'admin']);

            if ($total >= $limit) {
                $validation->error('role', 'adminUserLimitReached');
            }
        }
    }
}
