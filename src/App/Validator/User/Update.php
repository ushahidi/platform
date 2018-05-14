<?php

/**
 * Ushahidi User Update Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\User;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Traits\UserContext;

class Update extends Validator
{
    use UserContext;

    protected $default_error_source = 'user';
    protected $repo;
    protected $role_repo;
    protected $valid;

    public function __construct(UserRepository $repo, RoleRepository $role_repo, array $limits)
    {
        $this->repo = $repo;
        $this->role_repo = $role_repo;
        $this->limits = $limits;
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
        if ($this->limits['admin_users'] !== true && $role == 'admin') {
            $total = $this->repo->getTotalCount(['role' => 'admin']);

            if ($total >= $this->limits['admin_users']) {
                $validation->error('role', 'adminUserLimitReached');
            }
        }
    }
}
