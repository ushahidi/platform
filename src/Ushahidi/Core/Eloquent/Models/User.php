<?php
namespace Ushahidi\Core\Eloquent\Models;

use Ushahidi\Core\Entity\User as EntityUser;


class User implements EntityUser
{
    public function getId() { }

    public function getResource() { }

    public function asArray() { }

    public function setState(array $data) { }

    public function hasChanged($key, $array_key = null) { }

    public function getChanged() { }
}
