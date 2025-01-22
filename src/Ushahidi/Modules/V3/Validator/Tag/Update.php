<?php

/**
 * Ushahidi Tag Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Tag;

use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Contracts\Repository\Entity\RoleRepository;
use Ushahidi\Contracts\Repository\Usecase\UpdateTagRepository;

class Update extends LegacyValidator
{
    protected $repo;

    protected $role_repo;

    protected $default_error_source = 'tag';

    public function __construct(UpdateTagRepository $repo, RoleRepository $role_repo)
    {
        $this->repo = $repo;
        $this->role_repo = $role_repo;
    }

    protected function getRules()
    {
        return [
            'tag' => [
                ['min_length', [':value', 2]],
                ['max_length', [':value', 255]],
                // alphas, numbers, punctuation, and spaces
                ['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
            ],
            'parent_id' => [
                [[$this->repo, 'doesTagExist'], [':value']],
            ],
            'slug' => [
                ['min_length', [':value', 2]],
                [[$this->repo, 'isSlugAvailable'], [':value']],
            ],
            'description' => [
                // alphas, numbers, punctuation, and spaces
                ['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
            ],
            'type' => [
                ['in_array', [':value', ['category', 'status']]],
            ],
            'color' => [
                ['color'],
            ],
            'icon' => [
                // alphas, dashes and spaces
                ['regex', [':value', '/^[\pL\s\_\-]++$/uD']],
            ],
            'priority' => [
                ['digit'],
            ],
            'role' => [
                [[$this->role_repo, 'exists'], [':value']],
                [[$this->repo, 'isRoleValid'], [':validation', ':fulldata']]
            ]
        ];
    }
}
