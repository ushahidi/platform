<?php

namespace Ushahidi\Modules\V2\Mappers;

use Ushahidi\Modules\V2\Import;
use Ushahidi\Core\Ohanzee\Entities\User;
use Ushahidi\Modules\V2\Contracts\Mapper;

class UserMapper implements Mapper
{
    protected $roleMap = [
        'superadmin' => 'admin',
        'admin'      => 'admin',
        'login'      => 'user',
        'member'     => 'user'
    ];

    public function __invoke(Import $import, array $input) : array
    {
        $result = new User([
            'email' => $input['email'],
            'realname' => $input['name'] ?? '',
            'role' => $this->getRole($input['role']),
        ]);

        return [
            'result' => $result
        ];
    }

    protected function getRole($role)
    {
        $roles = explode(',', $role);

        return collect($roles)->reduce(function ($c, $item) {
            // If we already found an admin role
            if ($c === 'admin') {
                return 'admin';
            }

            // If role maps to admin
            if (isset($this->roleMap[$item]) && $this->roleMap[$item] === 'admin') {
                // Set v3 role to admin
                return 'admin';
            }

            // Otherwise map all other roles to user
            return 'user';
        }, 'user');
    }
}
