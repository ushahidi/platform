<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\User;

class UserMapper
{
    public function __invoke(array $input) : Entity
    {
        return new User([
            'email' => $input['email'],
            'realname' => $input['name'],
            // 'role' =>
        ]);
    }
}
