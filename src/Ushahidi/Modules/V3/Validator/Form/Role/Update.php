<?php

/**
 * Ushahidi Form Stage Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Form\Role;

use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Contracts\Repository\Entity\FormRepository;
use Ushahidi\Contracts\Repository\Entity\RoleRepository;

class Update extends LegacyValidator
{
    protected $form_repo;
    protected $role_repo;
    protected $default_error_source = 'form_role';

    public function setFormRepo(FormRepository $form_repo)
    {
        $this->form_repo = $form_repo;
    }

    public function setRoleRepo(RoleRepository $role_repo)
    {
        $this->role_repo = $role_repo;
    }

    protected function getRules()
    {
        return [
            'form_id' => [
                ['digit'],
                [[$this->form_repo, 'exists'], [':value']],
            ],
            'role_id' => [
                [[$this->role_repo, 'idExists'], [':value']],
            ],
        ];
    }
}
