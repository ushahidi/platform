<?php

namespace Ushahidi\App\ImportUshahidiV2\Contracts;

use Ushahidi\Core\Entity;

interface Mapper
{
    public function __invoke(array $input) : Entity;
}
